<?php

namespace ripnet\ssh;

use phpseclib\Net\SSH2;
use ripnet\ssh\Adapters\Adapters;



class SSH {
    protected $ip;
    protected $ssh;
    protected $adapter;

    public function __construct(string $ip, string $adapter) {
        $this->ip = $ip;
        $this->ssh = new SSH2($ip);
        if (!$this->adapter = Adapters::getAdapter($adapter)) {
            throw new \Exception(sprintf("Adapter not found: %s", $adapter));
        }
    }

    public function login(string $username, string $password): bool {
        if (array_key_exists('bad', $this->adapter)) {
            $success = $this->ssh->login($username);
        } else {
            $success = $this->ssh->login($username, $password);
        }
//        print $this->ssh->getLog();
//        print_r($success);
        if ($success) {
            if (array_key_exists('bad', $this->adapter)) {
                $this->ssh->read($this->adapter['bad'], SSH2::READ_REGEX);
                $this->ssh->write($password . $this->adapter['eol']);
            }

            if (array_key_exists('possible_banner', $this->adapter)) {
                $banner = '';
                $i = 0;
                $this->ssh->setTimeout(1);
                do {
                    $banner .= $this->ssh->read();
                    $i++;

                    if (preg_match('/' . $this->adapter['possible_banner'][0] . '/', $banner)) {
                        $this->ssh->write($this->adapter['possible_banner'][1]);
                        $this->readPrompt();
                        break;
                    }
                    if (preg_match($this->adapter['prompt'], $banner)) {
                        break;
                    }
                    //print "i: $i\n";
                } while ($i <= 5);
            } else {
                $this->readPrompt();
            }
            if (array_key_exists('disable_paging', $this->adapter)) {
                $this->writeln($this->adapter['disable_paging']);
                $this->readPrompt();
            }
        } else {
            print "nope";
        }

        return $success;
    }

    protected function readPrompt() {
        return $this->ssh->read($this->adapter['prompt'], SSH2::READ_REGEX);
    }

    protected function writeln(string $command) {
        $this->ssh->write($command . $this->adapter['eol']);
    }

    public function send(string $command) {
        $this->ssh->setTimeout(10);
        $this->writeln(trim($command));

        $results = [];
        $do = true;
        //$pad = [0, ''];
        while ($do) {
            $results = array_merge($results, explode($this->adapter['eol'], $this->readPrompt()));
            if (array_key_exists('paging', $this->adapter)) {
                if (preg_match($this->adapter['paging'][0], $results[count($results) - 1])) {
                    //$pad = [count($results) - 1, str_repeat(' ', strlen($results[count($results) - 1]))];
                    array_pop($results);
                    $this->ssh->write($this->adapter['paging'][1]);
                } else {
                    //$results[$pad[0]] = $pad[1] . $results[$pad[0]];
                    //$pad = [0, ''];
                    $do = false;
                }
            } else {
                $do = false;
            }
        }


        if (count($results) > 2) {
            array_shift($results);
            array_pop($results);
            $return = '';
            foreach ($results as $r) {
                $return .= "$r\n";
            }
            //print_r($this->ssh->getLog());
            return $return;
        } else {
            return '';
        }
    }


}
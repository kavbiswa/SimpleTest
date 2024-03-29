<?php

class ApplicationTestsuite extends TestSuite
{
    private $files = array();

    public function __construct()
    {
        // add a headline to know where we are ,)
        parent::__construct('Testsuite for "Application"');

        // walk through dir /unittests and grab all tests
        $this->scanDirForTests(__DIR__ . '/KochTest');

        // Debug array with test files
        // var_dump($this->files);

        if (count($this->files) > 0) {
            foreach ($this->files as $test_file) {
                // echo '<p>File '. $test_file.' was added to the tests.</p>';

                $this->addFile($test_file);
            }
        } else {
            echo 'No UnitTests found.' . PHP_EOL;
        }
    }

    public function scanDirForTests($dir)
    {
        if (is_dir($dir)) {
            $sourcedir = opendir($dir);
            while (false !== ( $file = readdir($sourcedir) )) {
                // fix slashes
                $source_file = strtr($dir . '/' . $file, '\\', '/');

                if (is_dir($source_file)) {
                    // exlude some dirs
                    if ($file == '.' || $file == '..' || $file == '.svn' || $file == 'fixtures') {
                        continue;
                    }

                    // WATCH IT ! RECURSION !
                    $this->scanDirForTests($source_file);
                } else {
                    if (is_file($source_file) && $this->isPHPfile($file)) {
                        /**
                         * Do not add WebTests, if PERFORM_WEBTESTS is off.
                         */
                        if (PERFORM_WEBTESTS == false && $this->isWebTestFile($file)) {
                            continue; // with next file in while loop
                        }

                        // add file to array
                        $this->files[] = realpath($source_file);

                        // echo "<p>File {$source_file} was added to the tests array.</p>\n";
                    }
                }
            }
            closedir($sourcedir);
        }
    }

    protected function isPHPFile($filename)
    {
        return preg_match('/\w+\.php$/', $filename);
    }

    protected function isWebTestFile($filename)
    {
        return preg_match('/webtest/i', $filename);
    }
}

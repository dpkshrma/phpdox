#!/bin/bash

echo "PHPDOX : Command Line Documentation Tool\n\n";
echo "Creating command line shortcut ...";
echo "phpdox() {">>~/.bashrc
echo "php $PWD/get_content.php \$1">>~/.bashrc
echo "}">>~/.bashrc
echo "Successful... Enjoy!\n\nUsage : phpdox <command_name>\n";
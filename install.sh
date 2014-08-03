#!/bin/bash

##############################
# phpdox installation script #
##############################

echo -e "PHPDOX : Command Line Documentation for PHP\n\n"
echo -e "Creating command line shortcut..."
echo -e "\n# Function to execute phpdox via command line" >> ~/.bashrc
echo -e "phpdox() {" >> ~/.bashrc
echo -e "php $PWD/get_content.php \$1" >> ~/.bashrc
echo -e "}" >> ~/.bashrc
echo -e "Installation successful\n\nUsage : phpdox <function_name>\n"
source ~/.bashrc

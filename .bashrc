#!/bin/bash
# Source this file for useful aliasses.
export EVOKE=$(dirname $BASH_SOURCE)
export EVOKE_SRC=$EVOKE/src/php/Evoke
export EVOKE_TEST=$EVOKE/test/php

alias cde='cd $EVOKE_SRC'
alias cdt='cd $EVOKE_TEST'
alias cdtu='cd $EVOKE_TEST/unit/Evoke'
alias pe='phing -f $EVOKE/build.xml'
alias pu='phpunit --bootstrap=$EVOKE_TEST/environment/Bootstrap.php'
alias te='phpunit -c "$EVOKE_TEST/environment/phpunit.xml"'

#!/bin/bash
# Source this file for useful aliasses.
export EVOKE=$(dirname $BASH_SOURCE)
export EVOKE_SRC=$EVOKE/php/src/Evoke
export EVOKE_TEST=$EVOKE/test

alias cde='cd $EVOKE_SRC'
alias cdt='cd $EVOKE_TEST'
alias te='phpunit --coverage-html $EVOKE_TEST/coverage $EVOKE_TEST/unit'



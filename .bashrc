#!/bin/bash
# Source this file for useful aliasses.
export EVOKE=$(dirname $BASH_SOURCE)

alias cde='cd $EVOKE/src'
alias cdt='cd $EVOKE/test'
alias te='phpunit --coverage-html $EVOKE/test/coverage $EVOKE/test/unit'



#!/bin/bash
# Source this file for useful aliasses.
export EVOKE=$(dirname $BASH_SOURCE)
export EVOKE_SRC=$EVOKE/src/php/Evoke
export EVOKE_TEST=$EVOKE/test/php

alias cde='cd $EVOKE_SRC'
alias cdeb='cd $EVOKE'
alias cdt='cd $EVOKE_TEST/unit/Evoke'
alias cdtb='cd $EVOKE_TEST'
alias pe='phing -f $EVOKE/build.xml'
alias pepd='pe php-documentor'
alias pepdu='pe php-documentor-update'
alias pepu='pe php-unit'

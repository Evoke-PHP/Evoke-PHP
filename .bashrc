#!/bin/bash
# Source this file for useful aliasses.
export EVOKE=$(dirname $BASH_SOURCE)
export EVOKE_SRC=$EVOKE/src
export EVOKE_TEST=$EVOKE/test

alias cde='cd $EVOKE_SRC'
alias cdeb='cd $EVOKE'
alias cdt='cd $EVOKE_TEST/unit/Evoke'
alias cdtb='cd $EVOKE_TEST'
alias pe='phing -f $EVOKE/build.xml'
alias ped='pe documentor'
alias pedu='pe documentor-update'
alias peu='pe unit'
alias peuq='pe unit-quick'

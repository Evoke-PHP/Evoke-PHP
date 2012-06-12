#!/bin/bash
# Source this file for useful aliasses.
export EVOKE=$(dirname $BASH_SOURCE)
export EVOKE_SRC=$EVOKE/php/src/Evoke
export EVOKE_TEST=$EVOKE/php/test

alias cde='cd $EVOKE_SRC'
alias cdt='cd $EVOKE_TEST'
alias cdtu='cd $EVOKE_TEST/unit/Evoke'
alias pe='phing -f $EVOKE/build.xml'
alias phpcse='phpcs --tab-width=4 --standard="$EVOKE/php/src/phpcs.xml"'
alias te='phpunit -c "$EVOKE_TEST/environment/phpunit.xml"'
#!/bin/bash

function Service::Broker::install() {
    Runtime::waitFor broker

    Console::start "${INFO}Configuring broker...${NC}"

    local output
    local tty
    [ -t -0 ] && tty='' || tty='-T'

    # shellcheck disable=SC2016
    output=$(
        Compose::command exec ${tty} \
            -e SPRYKER_RABBITMQ_VIRTUAL_HOSTS="${SPRYKER_RABBITMQ_VIRTUAL_HOSTS}" \
            -e SPRYKER_RABBITMQ_API_USERNAME="${SPRYKER_RABBITMQ_API_USERNAME}" \
            broker \
            bash -c 'for host in $(echo ${SPRYKER_RABBITMQ_VIRTUAL_HOSTS}); do rabbitmqctl add_vhost ${host}; rabbitmqctl set_permissions -p ${host} ${SPRYKER_RABBITMQ_API_USERNAME} ".*" ".*" ".*"; done'
    )
    Console::end "[DONE]"
    Console::verbose "${output}"
}

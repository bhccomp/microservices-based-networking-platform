FROM python:3.7-slim

RUN pip install --no-cache-dir requests

ADD https://raw.githubusercontent.com/rabbitmq/rabbitmq-management/v3.8.16/bin/rabbitmqadmin /usr/local/bin/
RUN chmod +x /usr/local/bin/rabbitmqadmin

COPY setup.sh /setup.sh
RUN chmod +x /setup.sh

CMD ["/rabbitmq.sh"]

ARG REGISTRY
FROM  ${REGISTRY}:69b413a

COPY --chown=resmush:resmush . .

ARG REGISTRY
FROM  ${REGISTRY}:base

COPY --chown=resmush:resmush . .

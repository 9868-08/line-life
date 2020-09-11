#!/usr/bin/env bash

indexer --config /etc/sphinxsearch/sphinx.conf  --rotate --all
searchd --config /etc/sphinxsearch/sphinx.conf --nodetach

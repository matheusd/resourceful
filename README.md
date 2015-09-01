# Resourceful

A RESTful library for PHP web applications

This is a simple library for architecturing PHP Web Applications in a RESTful way.

Endpoints of routes are mapped to Resources, upon which Actions (HTTP methods) are executed.

The main inspiration for this library is the Tonic PHP micro-framework. But this library aims to do less than Tonic: it just stablishes a few base classes for resources. The rest of the work for setting up a fully-featured framework is meant to be done by other libraries (eg. Pimple, Monolog, etc).
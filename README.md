[![Travis CI](https://img.shields.io/travis/rvelhote/libvat.svg)](https://travis-ci.org/rvelhote/libvat)
[![Code Climate](https://img.shields.io/codeclimate/github/rvelhote/libvat.svg)](https://codeclimate.com/github/rvelhote/libvat/issues)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/2a23466753b94690934c6988cb9b5a33)](https://www.codacy.com/app/rvelhote/libvat?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=rvelhote/libvat&amp;utm_campaign=Badge_Grade)

# Validate VAT Numbers
This is a PHP library to check the validity of VAT numbers across Europe. Most of the PHP libraries I have seen only 
validate the country code and the number of characters or are just checking the VIES webservice.

This library will be validating each VAT number against the check digit that is embedded in each number which is much 
more complete and will ensure that the number is actualy valid (at least according to the checkdigit algorithm).

Supported Countries
---------

This PHP library is still very much a work in progress and I am adding tests and (as much as possible) finding 
documentation about each country and algorithm before proceeding to the next country.

- Austria
- Canada
- Norway
- Poland
- Portugal
- Spain

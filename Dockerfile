#
# The MIT License (MIT)
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.
#
FROM ubuntu:yakkety
MAINTAINER Ricardo Velhote "rvelhote+github@gmail.com"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get -y upgrade && apt-get -y dist-upgrade && \
    apt-get install -y --no-install-recommends php7.0-cli php7.0-mbstring php-xdebug && \
    sed -i 's/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/g' /etc/php/7.0/cli/php.ini && \
    sed -i 's/display_errors = Off/display_errors = stderr/g' /etc/php/7.0/cli/php.ini && \
    sed -i 's/display_startup_errors = Off/display_startup_errors = On/g' /etc/php/7.0/cli/php.ini

WORKDIR /opt/application
CMD ["bash"]
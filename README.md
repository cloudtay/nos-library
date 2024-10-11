## Install

```bash
composer create-project cloudtay/nos@dev-main
cd nos
chmod +x bin/nos
./bin/nos # start server
```

> Access link: http://127.0.0.1:8008/

## ApacheBench test

```bash
ab -n 100000 -c 100 -k http://127.0.0.1:8008/hello
 
This is ApacheBench, Version 2.3 <$Revision: 1913912 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 10000 requests
Completed 20000 requests
Completed 30000 requests
Completed 40000 requests
Completed 50000 requests
Completed 60000 requests
Completed 70000 requests
Completed 80000 requests
Completed 90000 requests
Completed 100000 requests
Finished 100000 requests


Server Software:        ripple
Server Hostname:        127.0.0.1
Server Port:            8008

Document Path:          /hello
Document Length:        13 bytes

Concurrency Level:      100
Time taken for tests:   0.922 seconds
Complete requests:      100000
Failed requests:        0
Keep-Alive requests:    100000
Total transferred:      9200000 bytes
HTML transferred:       1300000 bytes
Requests per second:    108419.54 [#/sec] (mean)
Time per request:       0.922 [ms] (mean)
Time per request:       0.009 [ms] (mean, across all concurrent requests)
Transfer rate:          9740.82 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.1      0       4
Processing:     0    1   0.3      1       4
Waiting:        0    1   0.3      1       3
Total:          0    1   0.3      1       5

Percentage of the requests served within a certain time (ms)
  50%      1
  66%      1
  75%      1
  80%      1
  90%      1
  95%      2
  98%      2
  99%      2
 100%      5 (longest request)

```

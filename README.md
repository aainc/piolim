Piolim
====
this is yet another PHP Benchmark tool.

## Description



write and run like this

```php
<?php
print \Piolim\Benchmark\TimeThese::run('Benchmark name', function ($i) {
    // write code wants to test
    file_get_contents('http://aainc.co.jp');
});
```

and show you report like this
```
<< Benchmark name >>
[abstract]
total:12685.342788696 average:126.85342788696 min:102.67090797424 max:574.66697692871 std:48.994992410657

[distances]
A:99(0,31)
B:1(64,64)

4 = 4
3 = 25
2 = 32
1 = 29
0 = 2
5 = 3
6 = 2
11 = 1
17 = 1
64 = 1
___________________________________________________________________
| rank |      time       |      division      | number | distance |
|    1 | 102.67090797424 |    -24.18251991272 |     78 |        4 |
|    2 | 104.14505004883 |   -22.708377838135 |     93 |        4 |
|    3 | 105.92603683472 |   -20.927391052246 |     73 |        3 |
|    4 | 106.16612434387 |   -20.687303543091 |     50 |        3 |
|    5 |  106.2970161438 |   -20.556411743164 |     65 |        3 |
                        << snip >>
|   97 | 166.58282279968 |     39.72939491272 |     60 |        6 |
|   98 | 198.75192642212 |    71.898498535156 |     58 |       11 |
|   99 | 245.07880210876 |     118.2253742218 |     15 |       17 |
|  100 | 574.66697692871 |    447.81354904175 |      0 |       64 |
```

## Installation

```
git clone git@github.com:aainc/piolim.git
```

Not supported composer yet.

## Licence

The MIT License (MIT)

Copyright (c) [2014] aainc.co.jp

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Author

[aainc](https://github.com/aainc)

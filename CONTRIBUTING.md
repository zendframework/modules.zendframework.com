# Contributing

If you want to contribute, please be sure to read this file.


## Travis

This project is using Travis for automated builds, please refer to [`.travis.yml`](https://github.com/zendframework/modules.zendframework.com/blob/master/.travis.yml) 
to find out what is going on.

Basically, we run coding style checks and tests as part of the build. However, you can also run these checks and tests 
locally rather than keeping Travis busy doing it. There's not much more pleasing than a passing build, don't you agree?


## Coding Style

This projects follows the [PSR-2 Coding Style Guide](http://www.php-fig.org/psr/psr-2/). Be sure to read it!

Before opening a pull request or pushing more commits, fix coding style issues locally:

```
$ make cs
```


## Run tests

We do have a - somewhat - limited test suite, but are hoping for more. 

Please run the tests locally to see if they pass:

```
$ make test
```

## Contributors

:heart: Thank you very much for your [contributions](https://github.com/zendframework/modules.zendframework.com/graphs/contributors)!

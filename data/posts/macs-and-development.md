
## Not so bad actually

Macs are UNIX-like, and so they get many of the great benefits of the UNIX family: robust terminal access, over-the-counter C-compiler, and familiar syntax for the command line (coming from Linux).


## Except,

While coding one day, and needing to get a fix out, after Eclipse bit the dust on a large commit, but that's fine - I'll just use the command-line.

```
	$ svn commit cmd/myfile -m "Thing that was important"
	svn: E170000: Commit failed (details follow):
	svn: E170000: Unrecognized URL scheme for 'https://[redacted]/stable/cmd'
```

*Uh oh.* That's no good. Very simply, it's that svn wasn't compiled with http or https abilities. Having set up all the repositories in Eclipse _(who just got a black mark in my book when it crashed while commiting an external framework)_. Reasons, according to Ivan Zhakov of ServerFault fame:

> You compiled Subversion without serf library used for HTTP/HTTPS protocol: http://serf.apache.org/
> Specify serf library location using `--with-serf` configure option

Thank you, Ivan.


## So Off I Went

I downloaded `svn`'s sourcecode from Apache's site. `$ tar -xf su[tab]` opened that up. `$ cd su[tab]` into it, and `./configure --with-serf` (a standard UNIX pattern) kicked off the configuration.

Went well until hitting `configure: error: Serf was explicitly enabled but an appropriate version was not found.`. No problem though, just download the newest version of Serf. Another `$ tar -xf ser[tab]` and `$ cd ser[tab]` and I was ready to go.


## No Makefile Detected!

No `configure` file here, therefore: no makefile either. There was, however, a `SConstruct`. No need to open it up, to see what to do. That's obivously `scons`, a robust Python-based building system that essentially replaces `make` *and* ultimately, the whole build process. So I had `scons` installed for another C++ project that has an interesting build-system. `$ scons -j4` (for 4 hardware cores to run in parralel). _We are civilized, after all_. Lots of output, and no scary `Error: ^ on line`, so Detective James says it must have worked. A quick guess that it's `scons install` to put the final product in the right place on the filesystem. Correct.


## The Pieces Have Aligned - Our Diligence Comes to Fruition

And if I were a tyrant of the shadows, then that would be my catchphrase (if shadowy New World Order evil leaders had catchphrases). `$ cd ..` and `$ cd sub[tab]` get back to the start. Eyes on the clock? That's right, not long. And Eclipse is still chewing through the framework, and about to run out of memory again.

`$ ./configure --with-serf` again, and glorious configuration checks flood the terminal. Finally, no errors - and I invoke the command `$ make -j4`. Even with four cores, it takes a bit of time to mulch all that sweet code into a program. Eclipse tells me it ran out of memory (a gigabyte). I tell it that people can only help Eclipse until after Eclipse can admit it has a memory-eating issue.

At long last: the goal is near. `$ sudo make install` - and `Libraries have been installed in: /usr/local/libexec` is a sure indicator of success.


## Hit "Up" A Bunch, Then Hit Enter:

```
	$ svn commit cmd/myfile -m "Thing that was important"
	Sending        cmd/myfile
	Transmitting file data .done
	Committing transaction...
	Committed revision 700.
```

And a nice even number too. Hot damn.


## And Just For Kicks,

I'll have [Atom.io](http://atom.io) as my primary IDE starting now.

<div class="tags">Mac,UNIX,svn,shell,Bash,howto,Eclipse,scons</div>

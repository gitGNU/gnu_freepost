CHANGELOG for 2.6.x
===================

This changelog references the relevant changes (bug and security fixes) done
in 2.6 minor versions.

To get the diff for a specific change, go to https://github.com/symfony/symfony/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/symfony/symfony/compare/v2.6.0...v2.6.1

* 2.6.3 (2015-01-07)

 * bug #13286 [Security] Don't destroy the session on buggy php releases. (derrabus)

* 2.6.2 (2015-01-07)

 * feature #13241 [Form] add back model_timezone and view_timezone options (xabbuh)
 * bug #13297 [Process] Fix input reset in WindowsPipes (mpajunen)
 * bug #12417 [HttpFoundation] Fix an issue caused by php's Bug #66606. (wusuopu)
 * bug #13200 Don't add Accept-Range header on unsafe HTTP requests (jaytaph)
 * bug #12491 [Security] Don't send remember cookie for sub request (blanchonvincent)
 * bug #12574 [HttpKernel] Fix UriSigner::check when _hash is not at the end of the uri (nyroDev)
 * bug #13185 Fixes Issue #13184 - incremental output getters now return empty strings (Bailey Parker)
 * bug #13153 [TwigBridge] bootstrap_3_layout.html.twig inline form rendering button problem fix #13150  (edvinasme)
 * bug #13183 [DependencyInjection] force ExpressionLanguage version >= 2.6 (xabbuh)
 * bug #13173 [Debug] fixes ClassNotFoundFatalErrorHandler to correctly handle class not found errors with Symfony ClassLoader component autoloaders. (hhamon)
 * bug #13166 Fix a web profiler form issue with fields added to the form after the form was built (jakzal)
 * bug #12911 Fix wrong DateTransformer timezone param for non-UTC configuration (Soullivaneuh)
 * bug #13145 [DomCrawler] Fix behaviour with <base> tag (dkop, WouterJ)
 * bug #13027 fix #10054 - form data collector with dynamic fields (zulus)
 * bug #13141 [TwigBundle] Moved the setting of the default escaping strategy from the Twig engine to the Twig environment (fabpot)
 * bug #13114 [HttpFoundation] fixed error when an IP in the X-Forwarded-For HTTP head... (fabpot)
 * bug #12572 [HttpFoundation] fix checkip6 (Neime)
 * bug #13109 [Filesystem] restore ability to create broken symlinks (nicolas-grekas)
 * bug #13093 [TwigBundle] added missing absolute URL in Twig exceptions (fabpot)
 * bug #13087 [DependencyInjection] use/fix newest Definition::setFactory (nicolas-grekas)
 * bug #12975 [FrameworkBundle] Allow custom services for validator mapping cache. (jakzal)
 * bug #13068 Add LegacyPdoSessionHandler class (jeremylivingston)
 * bug #13075 [Config] fix error handler restoration in test (nicolas-grekas)
 * bug #13073 [VarDumper] fix and test PdoCaster (nicolas-grekas)
 * bug #13085 [FrameworkBundle] Fix dependency on ExtensionInterface over implementation (xphere)
 * bug #13081 [FrameworkBundle] forward error reporting level to insulated Client (nicolas-grekas)
 * bug #13053 [FrameworkBundle] Fixed Translation loader and update translation command. (saro0h)
 * bug #12900 [WebProfilerBundle] Fixed IE8 support (korotovsky)
 * bug #13047 [FrameworkBundle][Logging Translator] skip if param "translator.logging" doesn't exist. (aitboudad)
 * bug #13048 [Security] Delete old session on auth strategy migrate (xelaris)
 * bug #13035 Added the function providers as container resources (stof)
 * bug #13021 [FrameworkBundle] skip compiler pass if interface doesn't exist (xabbuh)
 * bug #12999 [FrameworkBundle] fix cache:clear command (nicolas-grekas)
 * bug #13004 add a limit and a test to FlattenExceptionTest. (Daniel Wehner)
 * bug #13013 Unify the way to provide expression functions for the DI container (stof)
 * bug #13009 [DebugBundle] fix link format handling with disabled templating (xabbuh)
 * bug #12996 [WebProfilerBundle] Fix placeholder date format (mvar)
 * bug #12961 fix session restart on PHP 5.3 (Tobion)
 * bug #12548 [Form] fixed a maxlength overring on a guessing (origaminal)
 * bug #12761 [Filesystem] symlink use RealPath instead LinkTarget (aitboudad)
 * bug #12848 [EventDispatcher] Fixed #12845 adding a listener to an event that is currently being dispatched (Pieter Jordaan)
 * bug #12935  [Security] Fixed ExpressionVoter - addExpressionLanguageProvider (Luca Genuzio)
 * bug #12855 [DependencyInjection] Perf php dumper (nicolas-grekas)
 * bug #12899 [WebProfiler] Tweaked ajax requests toolbar css reset (1ed)
 * bug #12913 Fix missing space in label_attr (garak)
 * bug #12894 [FrameworkBundle][Template name] avoid  error message for the shortcut n... (aitboudad)
 * bug #12806 [Console] Removed the use of $this->getHelperSet() as it is null by default (saro0h)
 * bug #12858 [ClassLoader] Fix undefined index in ClassCollectionLoader (szicsu)

* 2.6.1 (2014-12-03)

 * bug #12823 [DependencyInjection] fix PhpDumper (nicolas-grekas)
 * bug #12811 Configure firewall's kernel exception listener with configured entry point or a default entry point (rjkip)
 * bug #12770 [Filesystem] fix lock file permissions (nicolas-grekas)
 * bug #12784 [DependencyInjection] make paths relative to __DIR__ in the generated container (nicolas-grekas)
 * bug #12716 [ClassLoader] define constant only if it wasn't defined before (xabbuh)

* 2.6.0 (2014-11-28)

 * bug #12553 [Debug] fix error message on double exception (nicolas-grekas)
 * bug #12550 [FrameworkBundle] backport #12489 (xabbuh)
 * bug #12437  [Validator] make DateTime objects represented as strings in the violation message (hhamon)
 * bug #12575 [WebProfilerBundle] Remove usage of app.request in search bar template (jeromemacias)
 * bug #12570 Fix initialized() with aliased services (Daniel Wehner)

* 2.6.0-BETA2 (2014-11-23)

 * bug #12555 [Debug] fix ENT_SUBSTITUTE usage (nicolas-grekas)
 * feature #12538 [FrameworkBundle] be smarter when guessing the document root (xabbuh)
 * bug #12539 [TwigBundle] properly set request attributes in controller test (xabbuh)
 * bug #12267 [Form][WebProfiler] Empty form names fix (kix)
 * bug #12137 [FrameworkBundle] cache:clear command fills *.php.meta files with wrong data (Strate)
 * bug #12525 [Bundle][FrameworkBundle] be smarter when guessing the document root (xabbuh)
 * bug #12296 [SecurityBundle] Authentication entry point is only registered with firewall exception listener, not with authentication listeners (rjkip)
 * bug #12446 [Twig/DebugBundle] move dump extension registration (nicolas-grekas)
 * bug #12489 [FrameworkBundle] Fix server run in case the router script does not exist (romainneutron)
 * feature #12404 [Form] Remove timezone options from DateType and TimeType (jakzal)
 * bug #12487 [DomCrawler] Added support for 'link' tags in the Link class (StephaneSeng)
 * bug #12490 [FrameworkBundle] Fix server start in case the PHP binary is not found (romainneutron)
 * bug #12443 [HttpKernel] Adding support for invokable controllers in the RequestDataCollector (jameshalsall)
 * bug #12393 [DependencyInjection] inlined factory not referenced (boekkooi)
 * bug #12411 [VarDumper] Use Unicode Control Pictures (nicolas-grekas)
 * bug #12436 [Filesystem] Fixed case for empty folder (yosmanyga)

* 2.6.0-BETA1 (2014-11-03)

 * first beta release


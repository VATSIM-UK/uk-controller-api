# UK Controller Plugin API Changelog

## [6.29.3](https://github.com/VATSIM-UK/uk-controller-api/compare/6.29.2...6.29.3) (2022-10-30)


### Bug Fixes

* prevent mass delete of srd routes ([bddfb5f](https://github.com/VATSIM-UK/uk-controller-api/commit/bddfb5f2e587f4b2cb41fa4aea367060983e0c9d))

## [6.29.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.29.1...6.29.2) (2022-10-26)


### Bug Fixes

* prevent mass deletes of squawk assignments ([8dbf5c0](https://github.com/VATSIM-UK/uk-controller-api/commit/8dbf5c0cbae24fc8cbdce357b73bdaece5622b84))

## [6.29.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.29.0...6.29.1) (2022-10-25)


### Performance Improvements

* notification controllers query performance ([67992f1](https://github.com/VATSIM-UK/uk-controller-api/commit/67992f125cd7d7dd33cd18593b843f709d1fd714))

## [6.29.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.28.0...6.29.0) (2022-10-21)


### Features

* allow plugin versions to be deleted and restored ([f88ea4b](https://github.com/VATSIM-UK/uk-controller-api/commit/f88ea4b67929c2ee84ad456335fbf9f09f2f32f8))
* show plugin versions in the table ([6cc2331](https://github.com/VATSIM-UK/uk-controller-api/commit/6cc2331997ec6c80ec2816ee1b26e3c152ccf53c))

## [6.28.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.27.4...6.28.0) (2022-10-19)


### Features

* list squawk assignments in the plugin ([442f795](https://github.com/VATSIM-UK/uk-controller-api/commit/442f7954029ab148471fab3c08bc932d77d918e6))

## [6.27.4](https://github.com/VATSIM-UK/uk-controller-api/compare/6.27.3...6.27.4) (2022-10-19)


### Bug Fixes

* abort with 401 if socialite fails on login ([a575917](https://github.com/VATSIM-UK/uk-controller-api/commit/a5759171e89506ed8090baefb2012ea23f72aaed))
* **admin:** Ambiguous id on paired stand search ([476d13b](https://github.com/VATSIM-UK/uk-controller-api/commit/476d13b4e28f328861ff14b4e8ea229cb54bc85a))


### Performance Improvements

* fix n+1 query in msl retrieval ([8a7eb74](https://github.com/VATSIM-UK/uk-controller-api/commit/8a7eb74c1510da8639e58ce606c45148fa7bb8fb))

## [6.27.3](https://github.com/VATSIM-UK/uk-controller-api/compare/6.27.2...6.27.3) (2022-10-18)


### Bug Fixes

* dont optimise non existent tables ([dd34b14](https://github.com/VATSIM-UK/uk-controller-api/commit/dd34b14b0897b85f7a4f052cefd842df4dcab105))

## [6.27.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.27.1...6.27.2) (2022-10-10)


### Bug Fixes

* bulk detach on notifications ([ad928aa](https://github.com/VATSIM-UK/uk-controller-api/commit/ad928aa9d39467df76513b7fb9d244b627ce6bc6))

## [6.27.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.27.0...6.27.1) (2022-10-03)


### Bug Fixes

* disable bulk detach for controllers on notifications ([6971f26](https://github.com/VATSIM-UK/uk-controller-api/commit/6971f26109b0eba1914e4735f0f43122cce48d11))


### Miscellaneous Chores

* drop unused database table ([6a84d04](https://github.com/VATSIM-UK/uk-controller-api/commit/6a84d0492eb6c1bbb1aa1432f283e729a4f79907))
* remove seeder ([6882b59](https://github.com/VATSIM-UK/uk-controller-api/commit/6882b59c31cf66bbe5d06b52f01b18bc11e16bca))
* remove unused controller ([ca60fc2](https://github.com/VATSIM-UK/uk-controller-api/commit/ca60fc2097a51d64a679dc97cca14468b7c5dcc9))

## [6.27.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.26.0...6.27.0) (2022-10-02)


### Features

* display sids and stars in srd table ([df14440](https://github.com/VATSIM-UK/uk-controller-api/commit/df14440cb942fe24fa3df0bc263bd8555250a6e0))
* prepend sid to route on srd form ([5b67742](https://github.com/VATSIM-UK/uk-controller-api/commit/5b677426fe60c506f9efb7e0140de841c94e84f5))
* prepend sid to srd route segement ([154659e](https://github.com/VATSIM-UK/uk-controller-api/commit/154659ee0da86f04f7f2289f6200a88e0e274d38))

## [6.26.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.25.1...6.26.0) (2022-10-01)


### Features

* allow controllers to be selected by position type for notifications ([9cb95ca](https://github.com/VATSIM-UK/uk-controller-api/commit/9cb95cacb75d0a35f7454fe07ae2756769dfbb72))
* create and edit runways ([fafff0d](https://github.com/VATSIM-UK/uk-controller-api/commit/fafff0d89e6666ea40c029b1d242f1f596f95a58))
* display runway table ([4927a95](https://github.com/VATSIM-UK/uk-controller-api/commit/4927a9562334f7c2257519b8aeddf9ba0e240ddf))
* filter runways by airfield ([8e1a86e](https://github.com/VATSIM-UK/uk-controller-api/commit/8e1a86e55c71a34f0ea3a8c5a407c984066f1276))
* inverse runways ([f617a41](https://github.com/VATSIM-UK/uk-controller-api/commit/f617a41d75e087a076a0993ce485c09e16b1fa11))


### Performance Improvements

* cache runway select options ([bf47828](https://github.com/VATSIM-UK/uk-controller-api/commit/bf478287452c8e8f03cccce1b621e254ff5eece4))

## [6.25.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.25.0...6.25.1) (2022-09-30)


### Bug Fixes

* limit number of records to be displayed at once ([6a65113](https://github.com/VATSIM-UK/uk-controller-api/commit/6a65113cb71955f9957b4b582c02978964b30b24))

## [6.25.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.24.0...6.25.0) (2022-09-30)


### Features

* srd version on routes page ([846327b](https://github.com/VATSIM-UK/uk-controller-api/commit/846327b8ab824475609897e1f8e95688bd691d31))

## [6.24.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.23.0...6.24.0) (2022-09-30)


### Features

* filter srd notes ([b2918ea](https://github.com/VATSIM-UK/uk-controller-api/commit/b2918ea1bd34118a87415899f72385222ea84de2))
* list notes for an SRD route ([aab5d93](https://github.com/VATSIM-UK/uk-controller-api/commit/aab5d937b15fda03bbcd7a725cc59a788e74c1c5))
* view srd routes ([328c33b](https://github.com/VATSIM-UK/uk-controller-api/commit/328c33bf32c730d924e9c6f02faed655d71bc083))


### Bug Fixes

* remove create action for srd routes ([9dd5728](https://github.com/VATSIM-UK/uk-controller-api/commit/9dd572841efe07b243659c8a3e8ed84b459053aa))

## [6.23.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.22.0...6.23.0) (2022-09-30)


### Features

* calculate the current airac name ([c1965c0](https://github.com/VATSIM-UK/uk-controller-api/commit/c1965c0b5341e6080b7338f220e3e8e4f1ca78ed))


### Bug Fixes

* download srd from new source ([8b2e30c](https://github.com/VATSIM-UK/uk-controller-api/commit/8b2e30cb734af6ca3bdabfec9c05d1094a1ddd8a))

## [6.22.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.21.0...6.22.0) (2022-09-19)


### Features

* allow top-down ordering for airfields ([5fb3ff4](https://github.com/VATSIM-UK/uk-controller-api/commit/5fb3ff4d9780c694d436627f08a2023e55976944))
* automatically create default handoff for airfield ([0173963](https://github.com/VATSIM-UK/uk-controller-api/commit/017396318df1aa87644bc50638e84125d9ed8c29))
* dont display default handoffs on handoff page ([d4391d6](https://github.com/VATSIM-UK/uk-controller-api/commit/d4391d6a77e242e8fba22dead0ae54e46036d306))
* only show non airfield handoffs for sids ([76f2560](https://github.com/VATSIM-UK/uk-controller-api/commit/76f2560977ae7eb03f8401323bad6f64996c217f))
* updating controllers changes default handoff ([5e6ce0c](https://github.com/VATSIM-UK/uk-controller-api/commit/5e6ce0cc04fb0760e856cb732f4c2bcda200ac3d))


### Data Updates

* missing handoff orders ([198f4f0](https://github.com/VATSIM-UK/uk-controller-api/commit/198f4f027cd9572b2574ca9fde26b6ee8537e61d))

## [6.21.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.20.2...6.21.0) (2022-09-19)


### Features

* display airfields in a table ([1197f14](https://github.com/VATSIM-UK/uk-controller-api/commit/1197f140f6a6786e8a1f6e180d5706eb903c29f8))


### Bug Fixes

* model properties ([886ca6f](https://github.com/VATSIM-UK/uk-controller-api/commit/886ca6fd1c65901e81669eec74ff75f783959df1))


### Performance Improvements

* cache more select options ([4548443](https://github.com/VATSIM-UK/uk-controller-api/commit/45484430779b1a298629a07cfdcedc3825ed2d3c))

## [6.20.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.20.1...6.20.2) (2022-09-17)


### Performance Improvements

* stand table performance and ordering ([952bbda](https://github.com/VATSIM-UK/uk-controller-api/commit/952bbda15e225e4a80ab3cab236fc9f18d6424e5))

## [6.20.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.20.0...6.20.1) (2022-09-16)


### Performance Improvements

* select option caching ([677a1d8](https://github.com/VATSIM-UK/uk-controller-api/commit/677a1d8276f29d16d513c16dc69bdec52f86fe83))

## [6.20.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.19.0...6.20.0) (2022-09-16)


### Features

* controller position descriptions ([b7676ac](https://github.com/VATSIM-UK/uk-controller-api/commit/b7676ac12b3d741852d08dfebca864059f973327))
* table default sorts ([6926b6e](https://github.com/VATSIM-UK/uk-controller-api/commit/6926b6eb0b2b0a5c0854cece4d4d5eb4da52603f))

## [6.19.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.18.1...6.19.0) (2022-09-16)


### Features

* add wtc and size to stand table ([4b536c3](https://github.com/VATSIM-UK/uk-controller-api/commit/4b536c3dcbd0c168e6e2b5668d0e515d08844236))
* filters for stands table ([0ee9506](https://github.com/VATSIM-UK/uk-controller-api/commit/0ee95061d1ea08b1f62c4e0927b043b288af5341))


### Bug Fixes

* make stand airlines column non-orderable ([7141a09](https://github.com/VATSIM-UK/uk-controller-api/commit/7141a09bae7d64be4c0034deac0931c1152a0a67))
* remove delete bulk actions ([36a760b](https://github.com/VATSIM-UK/uk-controller-api/commit/36a760b204876fa21adb36f0a7749e6194791030))

## [6.18.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.18.0...6.18.1) (2022-09-15)


### Bug Fixes

* prevent bulk deletes ([05bc519](https://github.com/VATSIM-UK/uk-controller-api/commit/05bc519cf658d2d7e500ae5f565b8720073ef48d))

## [6.18.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.17.0...6.18.0) (2022-09-04)


### Features

* show sids in handoff table ([c8ecfbd](https://github.com/VATSIM-UK/uk-controller-api/commit/c8ecfbdde9916081463f9168c94e5b832faaec97))

## [6.17.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.16.3...6.17.0) (2022-09-03)


### Features

* filter active notifications only ([34ff41b](https://github.com/VATSIM-UK/uk-controller-api/commit/34ff41b359b0f8a7221f3c7e330f05d1efafda5f))
* filter read notifications ([720368f](https://github.com/VATSIM-UK/uk-controller-api/commit/720368f57d26f68ba399eb205798c3fb91f6561e))
* sort notifications by valid to by default ([dcbda80](https://github.com/VATSIM-UK/uk-controller-api/commit/dcbda800607ee3d71d39da7f1071668667523af7))

## [6.16.3](https://github.com/VATSIM-UK/uk-controller-api/compare/6.16.2...6.16.3) (2022-09-03)


### Bug Fixes

* make stands globally searchable ([7c04360](https://github.com/VATSIM-UK/uk-controller-api/commit/7c043607b41a19ad03f5a8dea2d6367a72c4d25f))
* no user searching ([1493efc](https://github.com/VATSIM-UK/uk-controller-api/commit/1493efcdc298ea3fb00865a9c72cdb2c96d4be81))

## [6.16.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.16.1...6.16.2) (2022-09-03)


### Bug Fixes

* branding colour in light mode ([8d6078b](https://github.com/VATSIM-UK/uk-controller-api/commit/8d6078b85ffb94df2e6ad50e8d46f827bfb8d982))

## [6.16.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.16.0...6.16.1) (2022-09-03)


### Bug Fixes

* fix filament version to work around bug ([1460c6d](https://github.com/VATSIM-UK/uk-controller-api/commit/1460c6da5786d45fc49ab183c24bb191d00c20a8))
* user searching in filament admin ([8d28457](https://github.com/VATSIM-UK/uk-controller-api/commit/8d28457aa5a11e1e31922cb7c8dd779d5fb4fd74))

## [6.16.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.15.0...6.16.0) (2022-08-29)


### Features

* create controller notifications ([61a6151](https://github.com/VATSIM-UK/uk-controller-api/commit/61a615166df5fddedf92be1ec94473f15c3935f6))
* filter notifications by controller ([a1edcb1](https://github.com/VATSIM-UK/uk-controller-api/commit/a1edcb1ed7528810db47c6b0941125e42f99a61d))
* front-page widgets ([e378006](https://github.com/VATSIM-UK/uk-controller-api/commit/e378006a9a7d097cacaf60f64fe65f9bfe4b626c))
* mark notification as read on view ([d1ad6f3](https://github.com/VATSIM-UK/uk-controller-api/commit/d1ad6f375d2995e67fb27737f97139ea316d625a))


### Bug Fixes

* fix notification date formats ([0c00404](https://github.com/VATSIM-UK/uk-controller-api/commit/0c00404ca9f8214316322778369269be265ccf61))


### Documentation

* remove redundant parts of the readme ([03f41a1](https://github.com/VATSIM-UK/uk-controller-api/commit/03f41a13809e9e8022b3925b3c343ef9695d1b72))

## [6.15.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.14.0...6.15.0) (2022-08-24)


### Features

* allow administration of navaids ([0d1001e](https://github.com/VATSIM-UK/uk-controller-api/commit/0d1001e06a87f23e88186cc846bb41180bdfb583))


### Bug Fixes

* hold access ([b0c85e9](https://github.com/VATSIM-UK/uk-controller-api/commit/b0c85e997b7d2957044b3b9119f0826404fcb704))

## [6.14.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.13.0...6.14.0) (2022-08-21)


### Features

* administer controller positions via filament ([7154670](https://github.com/VATSIM-UK/uk-controller-api/commit/7154670e6c88f74cf1619c27fc67bc52672cfc05))


### Bug Fixes

* number cast frequencies ([261d4fb](https://github.com/VATSIM-UK/uk-controller-api/commit/261d4fb370743a5ea82d5da2623764f6c37535a6))

## [6.13.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.12.0...6.13.0) (2022-08-21)


### Features

* allow controllers to be added to prenotes ([249426a](https://github.com/VATSIM-UK/uk-controller-api/commit/249426a823478c9bf0c6687c2f8930de5cc6be8b))
* allow creation and editing of prenotes ([09572e1](https://github.com/VATSIM-UK/uk-controller-api/commit/09572e1103c4adab6164896e73076ad36b7fc820))
* drop keys from prenotes ([87fb2b1](https://github.com/VATSIM-UK/uk-controller-api/commit/87fb2b1809717d52b702c7d4df0ea39166080ed6))

## [6.12.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.11.2...6.12.0) (2022-08-20)


### Features

* allow adding handoffs and reordering controllers ([71d3987](https://github.com/VATSIM-UK/uk-controller-api/commit/71d398781f9ce45c31d9d8f4a6fbb661562c9de1))
* lang ([d931902](https://github.com/VATSIM-UK/uk-controller-api/commit/d9319028fe75281333bef55bf051df5554bd4529))


### Bug Fixes

* ordering ([6af80b7](https://github.com/VATSIM-UK/uk-controller-api/commit/6af80b706af13f2354594ab957d47d92346ff2b2))
* remove unused provider ([91b6304](https://github.com/VATSIM-UK/uk-controller-api/commit/91b63047c01a3deff1813b39983ad33a7dd0a19a))

## [6.11.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.11.1...6.11.2) (2022-08-17)


### Bug Fixes

* add missing banned codes ([091411a](https://github.com/VATSIM-UK/uk-controller-api/commit/091411a3d55215c6d958b5f538ece608ec34eb01))
* dont assign squawk codes that are non-assignable ([cd83002](https://github.com/VATSIM-UK/uk-controller-api/commit/cd830026c891eb79c8baf8e7ed51767e215c89b9))

## [6.11.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.11.0...6.11.1) (2022-08-15)


### Miscellaneous Chores

* remove unused dependency code for airfield ownership ([2adb305](https://github.com/VATSIM-UK/uk-controller-api/commit/2adb3057fe6aef75a89709c37314bd6b06c3d91b))

## [6.11.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.10.0...6.11.0) (2022-08-02)


### Features

* associate prenotes with sids ([7449aa7](https://github.com/VATSIM-UK/uk-controller-api/commit/7449aa7a20d46fbc1a01b1ad6d7b4738a3d4658d))
* **sid:** allow sid administration ([bfcc580](https://github.com/VATSIM-UK/uk-controller-api/commit/bfcc5801112877898eb7fa040bff6e04a58dfabd))


### Bug Fixes

* ordering ([a89bbc2](https://github.com/VATSIM-UK/uk-controller-api/commit/a89bbc2bf673f33339e117f0f41f9fad2a3765dd))
* relation access ([fccaf58](https://github.com/VATSIM-UK/uk-controller-api/commit/fccaf58ec1ae8bfd22f7a4e647baebd50cf02425))
* remove test code ([86a40d1](https://github.com/VATSIM-UK/uk-controller-api/commit/86a40d1705f7f9595223f949ead7a0e0318f86e9))
* user permission auth ([4698d82](https://github.com/VATSIM-UK/uk-controller-api/commit/4698d82d0b2df19437a650973a30525fea970af9))


### Data Updates

* correct birmingham tower frequency ([b6c5aad](https://github.com/VATSIM-UK/uk-controller-api/commit/b6c5aad325d5877baa955bf0275d4e6c314b4082))

## [6.10.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.9.5...6.10.0) (2022-08-02)


### Features

* add activity log ([b853ece](https://github.com/VATSIM-UK/uk-controller-api/commit/b853ece322a243e85f7801ecd291598021b5fe41))
* allow administration of users in filament ([571bb8f](https://github.com/VATSIM-UK/uk-controller-api/commit/571bb8fa903dee0e6e6e3d042f2c1d379dc2f542))
* create roles and link users to roles ([d6205c4](https://github.com/VATSIM-UK/uk-controller-api/commit/d6205c4a6fc9341ee665d12d989e4f89d4232755))
* display user roles on front page ([f7b317f](https://github.com/VATSIM-UK/uk-controller-api/commit/f7b317f94e0f45ccd31e8c7799822cad3f636550))
* filament access policy ([3fbae27](https://github.com/VATSIM-UK/uk-controller-api/commit/3fbae272932814b814b85fd798adfe2fbae9bb8e))
* horizon access by role ([9792e64](https://github.com/VATSIM-UK/uk-controller-api/commit/9792e6435347aaf34d5fb2c06dc5944d8e924106))


### Bug Fixes

* migration running order ([1387087](https://github.com/VATSIM-UK/uk-controller-api/commit/138708753fc6408c26ad36dd7d25a2ca2296bd3e))
* relation manager model access ([1bf9ac2](https://github.com/VATSIM-UK/uk-controller-api/commit/1bf9ac2c02698ccf81d6de037a7dff1e98da1297))
* remove testing from auth controller ([7b28284](https://github.com/VATSIM-UK/uk-controller-api/commit/7b282846c7e076959e9ab36fd590644f5d2921e1))
* route middleware ([6d78e60](https://github.com/VATSIM-UK/uk-controller-api/commit/6d78e60539e40b738b3602fb773ea0286b04688a))


### Data Updates

* add web services roles ([5c9913b](https://github.com/VATSIM-UK/uk-controller-api/commit/5c9913b659b3a368effa8d2a67bfc83e7c7bdff5))

## [6.9.5](https://github.com/VATSIM-UK/uk-controller-api/compare/6.9.4...6.9.5) (2022-07-24)


### Bug Fixes

* horizon auth ([1b65dec](https://github.com/VATSIM-UK/uk-controller-api/commit/1b65dec705e070233af3277f13790893e080f53c))
* re-add missing model ([e1039f0](https://github.com/VATSIM-UK/uk-controller-api/commit/e1039f0e68d8e337bc448eb5d804f01f355cecd6))

## [6.9.4](https://github.com/VATSIM-UK/uk-controller-api/compare/6.9.3...6.9.4) (2022-07-20)


### Bug Fixes

* condition on stand identifier checking ([e54e294](https://github.com/VATSIM-UK/uk-controller-api/commit/e54e294b4fe248e335db8427e3148266ba46fd82))
* routes ([448326e](https://github.com/VATSIM-UK/uk-controller-api/commit/448326e263242d1e66a3c7db2e9eee76a72b0ab9))

## [6.9.3](https://github.com/VATSIM-UK/uk-controller-api/compare/6.9.2...6.9.3) (2022-07-12)


### Bug Fixes

* remove core redirects ([309fa4c](https://github.com/VATSIM-UK/uk-controller-api/commit/309fa4cad40a5773406b55e02af0fee0753d6339))

## [6.9.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.9.1...6.9.2) (2022-07-12)


### Bug Fixes

* let core do core things ([eefe991](https://github.com/VATSIM-UK/uk-controller-api/commit/eefe9919826fd5cc623c765eb57abd7688abc6a8))

## [6.9.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.9.0...6.9.1) (2022-07-12)


### Bug Fixes

* temporary core redirects ([416e57d](https://github.com/VATSIM-UK/uk-controller-api/commit/416e57d952a6c11f2ef2db6d17b808e09bf9bd0a))

## [6.9.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.8.3...6.9.0) (2022-07-12)


### Features

* make api the root of the api ([4d5d27f](https://github.com/VATSIM-UK/uk-controller-api/commit/4d5d27f2b6133a9010fe807c7adae66567797743))

## [6.8.3](https://github.com/VATSIM-UK/uk-controller-api/compare/6.8.2...6.8.3) (2022-06-27)


### Miscellaneous Chores

* update to laravel 9 ([64dada0](https://github.com/VATSIM-UK/uk-controller-api/commit/64dada014262aecb9195ea41a44e11c756a2a400))

## [6.8.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.8.1...6.8.2) (2022-06-26)


### Miscellaneous Chores

* **php:** drop support for PHP 7 ([abb2567](https://github.com/VATSIM-UK/uk-controller-api/commit/abb25671ab128b544af5e961689bddaab1aaaa54))

## [6.8.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.8.0...6.8.1) (2022-06-16)


### Bug Fixes

* fix stand dependency ([43d8942](https://github.com/VATSIM-UK/uk-controller-api/commit/43d8942451231ba76734a5fa3d356318b0d71b95))

## [6.8.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.7.5...6.8.0) (2022-06-16)


### Features

* **stands:** Ignore closed stands in dependency ([#950](https://github.com/VATSIM-UK/uk-controller-api/issues/950)) ([5fc729e](https://github.com/VATSIM-UK/uk-controller-api/commit/5fc729ecae5619bedd94101fde65c157142c74aa))


### Data Updates

* **holds:** update essex holds ([#938](https://github.com/VATSIM-UK/uk-controller-api/issues/938)) ([d956718](https://github.com/VATSIM-UK/uk-controller-api/commit/d956718d3dfdb953cc9b93bfa510925b1afa6e24)), closes [#937](https://github.com/VATSIM-UK/uk-controller-api/issues/937)
* **stands:** Manchester Pier 1 and Remote ([cd09237](https://github.com/VATSIM-UK/uk-controller-api/commit/cd09237680bd28ca8a286bff69de21a287f50942))

## [6.7.5](https://github.com/VATSIM-UK/uk-controller-api/compare/6.7.4...6.7.5) (2022-06-01)


### Data Updates

* **stands:** Heathrow stands 2022 ([6602bea](https://github.com/VATSIM-UK/uk-controller-api/commit/6602bea157d0d0b978af1a4ff86bdbf76f6ae6b8))

### [6.7.4](https://github.com/VATSIM-UK/uk-controller-api/compare/6.7.3...6.7.4) (2022-05-19)


### Data Updates

* **stands:** East midlands updates ([#926](https://github.com/VATSIM-UK/uk-controller-api/issues/926)) ([745dbce](https://github.com/VATSIM-UK/uk-controller-api/commit/745dbceb96d68ebc8c05745dc9001fb04e98a86c))

### [6.7.3](https://github.com/VATSIM-UK/uk-controller-api/compare/6.7.2...6.7.3) (2022-05-12)


### Bug Fixes

* **squawks:** prevent assignment of msfs default squawk ([#925](https://github.com/VATSIM-UK/uk-controller-api/issues/925)) ([deb5998](https://github.com/VATSIM-UK/uk-controller-api/commit/deb5998705694f996bba3272d1c08c6d177ae13a)), closes [#923](https://github.com/VATSIM-UK/uk-controller-api/issues/923)


### Data Updates

* **stands:** Update heathrow stand designation ([#924](https://github.com/VATSIM-UK/uk-controller-api/issues/924)) ([58e94be](https://github.com/VATSIM-UK/uk-controller-api/commit/58e94bea575d9806a91ab9bcdf92ea665ad00d44)), closes [#916](https://github.com/VATSIM-UK/uk-controller-api/issues/916)

### [6.7.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.7.1...6.7.2) (2022-04-29)


### Data Updates

* **wake:** Update recat category codes ([#911](https://github.com/VATSIM-UK/uk-controller-api/issues/911)) ([046a9dd](https://github.com/VATSIM-UK/uk-controller-api/commit/046a9ddf61bc1a1db0a04e137088f4ee876bbbf3)), closes [#910](https://github.com/VATSIM-UK/uk-controller-api/issues/910)

### [6.7.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.7.0...6.7.1) (2022-04-20)


### Bug Fixes

* **intention:** Intention code dependency path ([#909](https://github.com/VATSIM-UK/uk-controller-api/issues/909)) ([2e2fcfc](https://github.com/VATSIM-UK/uk-controller-api/commit/2e2fcfc9dce7319abf38aa9d59499f065ee263a2)), closes [#908](https://github.com/VATSIM-UK/uk-controller-api/issues/908)

## [6.7.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.6.0...6.7.0) (2022-04-20)


### Features

* **intention:** Add intention code data ([#903](https://github.com/VATSIM-UK/uk-controller-api/issues/903)) ([312cba8](https://github.com/VATSIM-UK/uk-controller-api/commit/312cba811603ad8918c89d1ecb8fe649c69b18b1))


### Data Updates

* **stands:** Play EGSS Stands ([#902](https://github.com/VATSIM-UK/uk-controller-api/issues/902)) ([8311daa](https://github.com/VATSIM-UK/uk-controller-api/commit/8311daaf69551070b0f51325c6716971758c40b8)), closes [#900](https://github.com/VATSIM-UK/uk-controller-api/issues/900)

## [6.6.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.5.0...6.6.0) (2022-04-15)


### Features

* **mapping:** Add display rules to dependency ([#895](https://github.com/VATSIM-UK/uk-controller-api/issues/895)) ([0a24f5d](https://github.com/VATSIM-UK/uk-controller-api/commit/0a24f5d3800ff87990e155d98b672833ee34dc70))


### Data Updates

* **stands:** Bristol stand updates ([#894](https://github.com/VATSIM-UK/uk-controller-api/issues/894)) ([17d86c6](https://github.com/VATSIM-UK/uk-controller-api/commit/17d86c6526899db279c14b2a03e6f33a7d8635ce)), closes [#693](https://github.com/VATSIM-UK/uk-controller-api/issues/693)

## [6.5.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.4.0...6.5.0) (2022-03-17)


### Features

* **mapping:** VRPs and Mapping Elements ([#874](https://github.com/VATSIM-UK/uk-controller-api/issues/874)) ([452a336](https://github.com/VATSIM-UK/uk-controller-api/commit/452a3367636f1302a8adfd5947c34041a07863c7))

## [6.4.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.3.0...6.4.0) (2022-03-13)


### Features

* **hold:** reduce hold entry distance ([#868](https://github.com/VATSIM-UK/uk-controller-api/issues/868)) ([f09b5b9](https://github.com/VATSIM-UK/uk-controller-api/commit/f09b5b9796e6bf760f92a01578fcf9cf15db1d09))


### Bug Fixes

* **stand:** handle aircraft with no UK WTC in arrival stand allocation ([#855](https://github.com/VATSIM-UK/uk-controller-api/issues/855)) ([8d1d76b](https://github.com/VATSIM-UK/uk-controller-api/commit/8d1d76b20282a88e8edbeea9b8704a2bcfd404a0)), closes [#854](https://github.com/VATSIM-UK/uk-controller-api/issues/854)


### Data Updates

* **prenotes:** remove stansted NUGBO prenotes ([#867](https://github.com/VATSIM-UK/uk-controller-api/issues/867)) ([4da6b75](https://github.com/VATSIM-UK/uk-controller-api/commit/4da6b75a0b7b8f5c69d50281b7534695b66bf459)), closes [#866](https://github.com/VATSIM-UK/uk-controller-api/issues/866)

## [6.3.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.2.2...6.3.0) (2022-03-11)


### Features

* **stand:** Allocate stands based on airline callsigns, add UKV preferred ([#865](https://github.com/VATSIM-UK/uk-controller-api/issues/865)) ([d9d882d](https://github.com/VATSIM-UK/uk-controller-api/commit/d9d882d6a014073aca85570ce9652f704add08ae))

### [6.2.2](https://github.com/VATSIM-UK/uk-controller-api/compare/6.2.1...6.2.2) (2022-02-25)


### Bug Fixes

* **stansted:** Sid identifiers didnt update ([#856](https://github.com/VATSIM-UK/uk-controller-api/issues/856)) ([5658e59](https://github.com/VATSIM-UK/uk-controller-api/commit/5658e59402c4bf7d54c7e2377bca44930099d596))

### [6.2.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.2.0...6.2.1) (2022-02-24)


### Data Updates

* **controllers:** Stansted positions and top down ([#824](https://github.com/VATSIM-UK/uk-controller-api/issues/824)) ([bf1ffeb](https://github.com/VATSIM-UK/uk-controller-api/commit/bf1ffeb7b8d7297a5da3d1fea5f1c916476de811))
* **controllers:** Update controller positions to properly reflect 25khz spacing ([#836](https://github.com/VATSIM-UK/uk-controller-api/issues/836)) ([871b57a](https://github.com/VATSIM-UK/uk-controller-api/commit/871b57af78287a7bb723795649db5ef3e43d4ea8)), closes [#828](https://github.com/VATSIM-UK/uk-controller-api/issues/828) [#817](https://github.com/VATSIM-UK/uk-controller-api/issues/817)
* **stansted:** stansted sid identifier updates ([#825](https://github.com/VATSIM-UK/uk-controller-api/issues/825)) ([f2c8ba5](https://github.com/VATSIM-UK/uk-controller-api/commit/f2c8ba529aba6ca420e8df1413ff7d28ebc1ae5b)), closes [#818](https://github.com/VATSIM-UK/uk-controller-api/issues/818)

## [6.2.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.1.0...6.2.0) (2022-02-22)


### Features

* **wake:** Add arrival wake intervals ([#845](https://github.com/VATSIM-UK/uk-controller-api/issues/845)) ([4acf2ee](https://github.com/VATSIM-UK/uk-controller-api/commit/4acf2ee51eccb7775109cb8b75207201eafa88d9))

## [6.1.0](https://github.com/VATSIM-UK/uk-controller-api/compare/6.0.1...6.1.0) (2022-02-19)


### Features

* **hold:** Track aircraft in hold proximity ([#810](https://github.com/VATSIM-UK/uk-controller-api/issues/810)) ([90d7be6](https://github.com/VATSIM-UK/uk-controller-api/commit/90d7be606b857b1b826da22556a7499a1ce059d0))

### [6.0.1](https://github.com/VATSIM-UK/uk-controller-api/compare/6.0.0...6.0.1) (2022-02-07)


### Bug Fixes

* **stands:** Fix Jet2 Glasgow Allocations ([#837](https://github.com/VATSIM-UK/uk-controller-api/issues/837)) ([41671b5](https://github.com/VATSIM-UK/uk-controller-api/commit/41671b558cf250f2941b3620cb86a45735627a1e)), closes [#830](https://github.com/VATSIM-UK/uk-controller-api/issues/830)

## [6.0.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.9.6...6.0.0) (2022-02-07)


### ⚠ BREAKING CHANGES

* **wake:** Old dependencies no longer available

### Features

* **wake:** remove old wake category dependencies ([#827](https://github.com/VATSIM-UK/uk-controller-api/issues/827)) ([86bb49d](https://github.com/VATSIM-UK/uk-controller-api/commit/86bb49d9526b23c4b5a1ae1f96249fb60f50bde3)), closes [#822](https://github.com/VATSIM-UK/uk-controller-api/issues/822)

### [5.9.6](https://github.com/VATSIM-UK/uk-controller-api/compare/5.9.5...5.9.6) (2022-02-07)


### Data Updates

* **dependencies:** add missing tables for wake scheme dependency ([#826](https://github.com/VATSIM-UK/uk-controller-api/issues/826)) ([5438466](https://github.com/VATSIM-UK/uk-controller-api/commit/5438466586bdc8927ce306003d575ea007c52815)), closes [#821](https://github.com/VATSIM-UK/uk-controller-api/issues/821)

### [5.9.5](https://github.com/VATSIM-UK/uk-controller-api/compare/5.9.4...5.9.5) (2022-02-05)


### Data Updates

* **wake:** Add intermediate recat intervals ([#807](https://github.com/VATSIM-UK/uk-controller-api/issues/807)) ([e5ecd3c](https://github.com/VATSIM-UK/uk-controller-api/commit/e5ecd3c1b2d47908b1651d30e4c2ebc76a4d00bd)), closes [#806](https://github.com/VATSIM-UK/uk-controller-api/issues/806)

### [5.9.4](https://github.com/VATSIM-UK/uk-controller-api/compare/5.9.3...5.9.4) (2022-02-03)


### Data Updates

* **holds:** 2201 hold updates ([#809](https://github.com/VATSIM-UK/uk-controller-api/issues/809)) ([6fbb115](https://github.com/VATSIM-UK/uk-controller-api/commit/6fbb1154c47d5c4905f636d54325260a148e6545)), closes [#808](https://github.com/VATSIM-UK/uk-controller-api/issues/808)
* **sids:** make gatwick biggin departures non runway specific ([#805](https://github.com/VATSIM-UK/uk-controller-api/issues/805)) ([17805dd](https://github.com/VATSIM-UK/uk-controller-api/commit/17805dd77cf827f9a9104b793c4e8a355a9711d0)), closes [#804](https://github.com/VATSIM-UK/uk-controller-api/issues/804)
* **stands:** Stansted A380 stands ([#812](https://github.com/VATSIM-UK/uk-controller-api/issues/812)) ([e23e63c](https://github.com/VATSIM-UK/uk-controller-api/commit/e23e63c3054dd1acda8e8f3f16cd3cce220a0aea)), closes [#881](https://github.com/VATSIM-UK/uk-controller-api/issues/881)

### [5.9.3](https://github.com/VATSIM-UK/uk-controller-api/compare/5.9.2...5.9.3) (2022-01-23)


### Data Updates

* **prenotes:** Allow prenotes to certain tower positions ([#793](https://github.com/VATSIM-UK/uk-controller-api/issues/793)) ([f337c6a](https://github.com/VATSIM-UK/uk-controller-api/commit/f337c6ae4f46e739f7a7941f7305eee22746a200))

### [5.9.2](https://github.com/VATSIM-UK/uk-controller-api/compare/5.9.1...5.9.2) (2022-01-20)


### Bug Fixes

* **metars:** Fix metar caching ([#791](https://github.com/VATSIM-UK/uk-controller-api/issues/791)) ([9ae2940](https://github.com/VATSIM-UK/uk-controller-api/commit/9ae294020c14af14f3531e1d92492f9fa408a2d1)), closes [#790](https://github.com/VATSIM-UK/uk-controller-api/issues/790)

### [5.9.1](https://github.com/VATSIM-UK/uk-controller-api/compare/5.9.0...5.9.1) (2022-01-20)


### Data Updates

* **stands:** EGNX cargo stand priority ([#792](https://github.com/VATSIM-UK/uk-controller-api/issues/792)) ([cb54d27](https://github.com/VATSIM-UK/uk-controller-api/commit/cb54d2763ade4d0f69ed2348fc98006cb879a640))

## [5.9.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.8.2...5.9.0) (2022-01-20)


### Features

* **wake:** Wake tidy up ([#781](https://github.com/VATSIM-UK/uk-controller-api/issues/781)) ([9f1f2e9](https://github.com/VATSIM-UK/uk-controller-api/commit/9f1f2e96cce1b11fbb0e93145e1c51db8046d2a4))

### [5.8.2](https://github.com/VATSIM-UK/uk-controller-api/compare/5.8.1...5.8.2) (2022-01-11)


### Bug Fixes

* **metars:** broadcast metar data in the correct format ([#775](https://github.com/VATSIM-UK/uk-controller-api/issues/775)) ([b8d5b20](https://github.com/VATSIM-UK/uk-controller-api/commit/b8d5b204583c1771ae1ddb8fb2c240cdcc5a9a0d)), closes [#774](https://github.com/VATSIM-UK/uk-controller-api/issues/774)

### [5.8.1](https://github.com/VATSIM-UK/uk-controller-api/compare/5.8.0...5.8.1) (2022-01-07)


### Bug Fixes

* **runways:** Remove PHP 8 specific syntax ([19febe5](https://github.com/VATSIM-UK/uk-controller-api/commit/19febe592212562e07aa9b1dfd01c23c353edfd3))

## [5.8.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.7.0...5.8.0) (2022-01-07)


### Features

* **runways:** Add runway data ([#765](https://github.com/VATSIM-UK/uk-controller-api/issues/765)) ([f590e08](https://github.com/VATSIM-UK/uk-controller-api/commit/f590e08c7425a296e2f8c54aafdd1857c57e0260))

## [5.7.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.6.4...5.7.0) (2022-01-05)


### Features

* **versions:** Don't delete old versions on new release ([#772](https://github.com/VATSIM-UK/uk-controller-api/issues/772)) ([bfade2f](https://github.com/VATSIM-UK/uk-controller-api/commit/bfade2f3e1cfaf9175c1b20db8a31fcdd9ed7915))

### [5.6.4](https://github.com/VATSIM-UK/uk-controller-api/compare/5.6.3...5.6.4) (2022-01-05)


### Data Updates

* **events:** Add Heathrow realops arrival stand allocations ([#762](https://github.com/VATSIM-UK/uk-controller-api/issues/762)) ([92f98d7](https://github.com/VATSIM-UK/uk-controller-api/commit/92f98d7eb5c265302a53285cccbcfc37dd32b56d))

### [5.6.3](https://github.com/VATSIM-UK/uk-controller-api/compare/5.6.2...5.6.3) (2022-01-04)


### Bug Fixes

* **releases:** Remove PHP 8 syntax to work in 7.4 for now ([#769](https://github.com/VATSIM-UK/uk-controller-api/issues/769)) ([b2e365a](https://github.com/VATSIM-UK/uk-controller-api/commit/b2e365abf7af2d4eb721fd5633e14f96b1ab2f93))

### [5.6.2](https://github.com/VATSIM-UK/uk-controller-api/compare/5.6.1...5.6.2) (2022-01-04)


### Bug Fixes

* **versions:** Remove a PHP8-ism ([3abb756](https://github.com/VATSIM-UK/uk-controller-api/commit/3abb756854847bb74b3573d7ff494d69f805d9c2))

### [5.6.1](https://github.com/VATSIM-UK/uk-controller-api/compare/5.6.0...5.6.1) (2022-01-04)


### Bug Fixes

* **versions:** Fix patch to work with deleted versions on production ([#768](https://github.com/VATSIM-UK/uk-controller-api/issues/768)) ([3596881](https://github.com/VATSIM-UK/uk-controller-api/commit/3596881f75c606ee1d2eb07dfe96fae9e93716d9))

## [5.6.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.5.1...5.6.0) (2022-01-04)


### Features

* **plugin:** Allow pre-release versions of the plugin ([#758](https://github.com/VATSIM-UK/uk-controller-api/issues/758)) ([69a2d16](https://github.com/VATSIM-UK/uk-controller-api/commit/69a2d16752067df39569e2f4a284ccebfa235b6d))

### [5.5.1](https://github.com/VATSIM-UK/uk-controller-api/compare/5.5.0...5.5.1) (2022-01-03)


### Data Updates

* **stands:** Update aberdeen stand allocations ([#757](https://github.com/VATSIM-UK/uk-controller-api/issues/757)) ([b86bbbe](https://github.com/VATSIM-UK/uk-controller-api/commit/b86bbbec2b82d8c5902bb9c6e2c31711c394da71))

## [5.5.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.4.2...5.5.0) (2022-01-03)


### Features

* **release:** Allow remarks when rejecting or accepting a release ([#760](https://github.com/VATSIM-UK/uk-controller-api/issues/760)) ([87b4209](https://github.com/VATSIM-UK/uk-controller-api/commit/87b420970842b6c9bcb5b4e0945514b52ff1fc20))

### [5.4.2](https://github.com/VATSIM-UK/uk-controller-api/compare/5.4.1...5.4.2) (2022-01-03)


### Bug Fixes

* **kernel:** Remove schedule monitor sync ([#753](https://github.com/VATSIM-UK/uk-controller-api/issues/753)) ([543c26f](https://github.com/VATSIM-UK/uk-controller-api/commit/543c26f2b40717b762e8a8dc8f9a38747721eaad))

### [5.4.1](https://github.com/VATSIM-UK/uk-controller-api/compare/5.4.0...5.4.1) (2022-01-03)


### Data Updates

* **edinburgh:** close edinburgh stands 9A, 10A, 209 for allocation ([#748](https://github.com/VATSIM-UK/uk-controller-api/issues/748)) ([ddd13cb](https://github.com/VATSIM-UK/uk-controller-api/commit/ddd13cb4c5023ea0ee83c83429d1646cefa5cf54)), closes [#746](https://github.com/VATSIM-UK/uk-controller-api/issues/746)
* **holds:** update cardiff hold inbound heading ([#747](https://github.com/VATSIM-UK/uk-controller-api/issues/747)) ([bc822f7](https://github.com/VATSIM-UK/uk-controller-api/commit/bc822f7baea2cb5a7ed715f853cc96eeb5760ad9)), closes [#745](https://github.com/VATSIM-UK/uk-controller-api/issues/745)

## [5.4.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.3.0...5.4.0) (2021-12-20)


### Features

* Add PHP 8.1 Support ([#575](https://github.com/VATSIM-UK/uk-controller-api/issues/575)) ([00189df](https://github.com/VATSIM-UK/uk-controller-api/commit/00189df94e6930b33e345995f9a194f784b96d19))

## [5.3.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.2.0...5.3.0) (2021-12-19)


### Features

* **stands:** Cargo airline preferences and gravity va preferred stands ([#733](https://github.com/VATSIM-UK/uk-controller-api/issues/733)) ([e093a71](https://github.com/VATSIM-UK/uk-controller-api/commit/e093a7194b298de611113044bcdd2183ccc90066))

## [5.2.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.1.0...5.2.0) (2021-12-19)


### Features

* **airfields:** Add a default handoff order for airfield departures ([#738](https://github.com/VATSIM-UK/uk-controller-api/issues/738)) ([4c3c5a6](https://github.com/VATSIM-UK/uk-controller-api/commit/4c3c5a675c8d124c57c4e5843456ddd61368d2b4))

## [5.1.0](https://github.com/VATSIM-UK/uk-controller-api/compare/5.0.0...5.1.0) (2021-12-19)


### Features

* **metars:** Improve metar parsing ([#731](https://github.com/VATSIM-UK/uk-controller-api/issues/731)) ([91f22f9](https://github.com/VATSIM-UK/uk-controller-api/commit/91f22f9ad31d802dba74e0fc59b81672792632a9))

## [5.0.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.13.2...5.0.0) (2021-12-14)


### ⚠ BREAKING CHANGES

* The previous refactor commit removes old functionality

* Bump major version ([f08c6fd](https://github.com/VATSIM-UK/uk-controller-api/commit/f08c6fd7fad12009ee04cc3ce8602ddaf0b7e2f4))

### [4.13.2](https://github.com/VATSIM-UK/uk-controller-api/compare/4.13.1...4.13.2) (2021-12-05)


### Bug Fixes

* **stands:** properly deduplicate stand occupancy ([#732](https://github.com/VATSIM-UK/uk-controller-api/issues/732)) ([e44045f](https://github.com/VATSIM-UK/uk-controller-api/commit/e44045f64de4b645d99f059453866ba37281eb47))

### [4.13.1](https://github.com/VATSIM-UK/uk-controller-api/compare/4.13.0...4.13.1) (2021-12-05)


### Bug Fixes

* **stands:** only assign departure stands to one aircraft per stand  ([#719](https://github.com/VATSIM-UK/uk-controller-api/issues/719)) ([b77bccd](https://github.com/VATSIM-UK/uk-controller-api/commit/b77bccdeb876ffae1c1ee28b9908f8b3bab2d9b2)), closes [#716](https://github.com/VATSIM-UK/uk-controller-api/issues/716) [#713](https://github.com/VATSIM-UK/uk-controller-api/issues/713)

## [4.13.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.12.2...4.13.0) (2021-12-05)


### Features

* **missedapproach:** Allow missed approaches to be acknowledged ([#721](https://github.com/VATSIM-UK/uk-controller-api/issues/721)) ([2a78c05](https://github.com/VATSIM-UK/uk-controller-api/commit/2a78c05c43e9c503033d401735b790a3a0383c3a)), closes [#716](https://github.com/VATSIM-UK/uk-controller-api/issues/716)

### [4.12.2](https://github.com/VATSIM-UK/uk-controller-api/compare/4.12.1...4.12.2) (2021-11-30)


### Data Updates

* **prenotes:** Add missing flight rule to prenote ([#728](https://github.com/VATSIM-UK/uk-controller-api/issues/728)) ([b76e7c1](https://github.com/VATSIM-UK/uk-controller-api/commit/b76e7c17364982ae3ab8e9b440a1a71ebdb7b52e))

### [4.12.1](https://github.com/VATSIM-UK/uk-controller-api/compare/4.12.0...4.12.1) (2021-11-28)


### Bug Fixes

* **dependencies:** Automatically add missing dependency tables ([#724](https://github.com/VATSIM-UK/uk-controller-api/issues/724)) ([dab8a17](https://github.com/VATSIM-UK/uk-controller-api/commit/dab8a174d3a0f334a0e04f7203759fefef7da16a))

## [4.12.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.11.3...4.12.0) (2021-11-28)


### Features

* **dependency:** Consolidate dependencies ([#697](https://github.com/VATSIM-UK/uk-controller-api/issues/697)) ([8682627](https://github.com/VATSIM-UK/uk-controller-api/commit/8682627d5b33079fe533695211074258fcfa389d))

### [4.11.3](https://github.com/VATSIM-UK/uk-controller-api/compare/4.11.2...4.11.3) (2021-11-28)


### Bug Fixes

* **dependencies:** Remove part of migration that is blocking on live ([#723](https://github.com/VATSIM-UK/uk-controller-api/issues/723)) ([84f57c8](https://github.com/VATSIM-UK/uk-controller-api/commit/84f57c892132ceee2e4fa7e64e4c532c735854ee))

### [4.11.2](https://github.com/VATSIM-UK/uk-controller-api/compare/4.11.1...4.11.2) (2021-11-28)


### Bug Fixes

* **database:** allow dependency updates to run in transaction ([#717](https://github.com/VATSIM-UK/uk-controller-api/issues/717)) ([d11debe](https://github.com/VATSIM-UK/uk-controller-api/commit/d11debebb1f2f506d853e632b0c39229e41ee339)), closes [#716](https://github.com/VATSIM-UK/uk-controller-api/issues/716)
* **network:** invalid json from network metadata ([#715](https://github.com/VATSIM-UK/uk-controller-api/issues/715)) ([534612a](https://github.com/VATSIM-UK/uk-controller-api/commit/534612afc33c152c20c473759f07aca076c4eadd))
* **regionalpressures:** default negative regional pressures to zero ([#718](https://github.com/VATSIM-UK/uk-controller-api/issues/718)) ([c05a9c6](https://github.com/VATSIM-UK/uk-controller-api/commit/c05a9c69492f04b3c26b39b436a95415414bcb66)), closes [#716](https://github.com/VATSIM-UK/uk-controller-api/issues/716) [#712](https://github.com/VATSIM-UK/uk-controller-api/issues/712)

### [4.11.1](https://github.com/VATSIM-UK/uk-controller-api/compare/4.11.0...4.11.1) (2021-11-16)


### Bug Fixes

* **database:** prune failed jobs daily ([#709](https://github.com/VATSIM-UK/uk-controller-api/issues/709)) ([b958c95](https://github.com/VATSIM-UK/uk-controller-api/commit/b958c95d3002736b9375aaa9417f79afefbe14d4))

## [4.11.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.10.0...4.11.0) (2021-11-16)


### Features

* **dependency:** Automatic updates of dependencies ([#696](https://github.com/VATSIM-UK/uk-controller-api/issues/696)) ([01fcd67](https://github.com/VATSIM-UK/uk-controller-api/commit/01fcd67086c4c1c96393e694bcf3729ac70df1d4))

## [4.10.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.9.4...4.10.0) (2021-11-16)


### Features

* **controllers:** Track who's controlling on the network ([#693](https://github.com/VATSIM-UK/uk-controller-api/issues/693)) ([2a4a879](https://github.com/VATSIM-UK/uk-controller-api/commit/2a4a8790e21604fceb8163cf09b95bb0a68b073d))

### [4.9.4](https://github.com/VATSIM-UK/uk-controller-api/compare/4.9.3...4.9.4) (2021-11-13)


### Bug Fixes

* **callsigns:** Increase length of VATSIM callsigns to new network limit ([#707](https://github.com/VATSIM-UK/uk-controller-api/issues/707)) ([c8cd474](https://github.com/VATSIM-UK/uk-controller-api/commit/c8cd474544215d6dce0475691bb273add54b49ae))

### [4.9.3](https://github.com/VATSIM-UK/uk-controller-api/compare/4.9.2...4.9.3) (2021-11-07)


### Data Updates

* **military:** leeming and culdrose squawk ranges ([#701](https://github.com/VATSIM-UK/uk-controller-api/issues/701)) ([84e2410](https://github.com/VATSIM-UK/uk-controller-api/commit/84e2410ef914bd2e4394deafded29ba3c1a98097))

### [4.9.2](https://github.com/VATSIM-UK/uk-controller-api/compare/4.9.1...4.9.2) (2021-10-14)


### Data Updates

* **edinburgh:** close stands ([#688](https://github.com/VATSIM-UK/uk-controller-api/issues/688)) ([ad0a9e0](https://github.com/VATSIM-UK/uk-controller-api/commit/ad0a9e00dbd8fe865d008c00506a6660682380df))

### [4.9.1](https://github.com/VATSIM-UK/uk-controller-api/compare/4.9.0...4.9.1) (2021-10-08)


### Data Updates

* **egnc:** Update carlisle atc positions ([#682](https://github.com/VATSIM-UK/uk-controller-api/issues/682)) ([68d6e9a](https://github.com/VATSIM-UK/uk-controller-api/commit/68d6e9af9e478e67306eebe08d45d5499d3a9c69))
* **sids:** New Channel Island SIDs ([#681](https://github.com/VATSIM-UK/uk-controller-api/issues/681)) ([a3a3759](https://github.com/VATSIM-UK/uk-controller-api/commit/a3a3759cf54e9e897acf7db2c3722f393afd607a))

## [4.9.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.8.4...4.9.0) (2021-09-27)


### Features

* **missed-approach:** Automated missed approach notifications ([#654](https://github.com/VATSIM-UK/uk-controller-api/issues/654)) ([d97f942](https://github.com/VATSIM-UK/uk-controller-api/commit/d97f942ecd83ec6cae08d0c71d2fe86d23912ff4))
* **stands:** Allow stands to be temporarily closed ([#659](https://github.com/VATSIM-UK/uk-controller-api/issues/659)) ([9ca6010](https://github.com/VATSIM-UK/uk-controller-api/commit/9ca60104b3001ffc9624da95d7857962d4e9e00b))

### [4.8.4](https://github.com/VATSIM-UK/uk-controller-api/compare/4.8.3...4.8.4) (2021-09-18)


### Bug Fixes

* **prenotes:** Allow ground and delivery to prenote ([ce8a746](https://github.com/VATSIM-UK/uk-controller-api/commit/ce8a746f41d0471c956d821d6855cfdec4b3aef8))

### [4.8.3](https://github.com/VATSIM-UK/uk-controller-api/compare/4.8.2...4.8.3) (2021-09-09)


### Bug Fixes

* **srd:** Update SRD download url ([#657](https://github.com/VATSIM-UK/uk-controller-api/issues/657)) ([4c80fe1](https://github.com/VATSIM-UK/uk-controller-api/commit/4c80fe1d2d4a966946336eacb7f789074c67b2ec))

### [4.8.2](https://github.com/VATSIM-UK/uk-controller-api/compare/4.8.1...4.8.2) (2021-09-06)


### Bug Fixes

* **prenotes:** Broadcast prenote messages ([#648](https://github.com/VATSIM-UK/uk-controller-api/issues/648)) ([03fb5b7](https://github.com/VATSIM-UK/uk-controller-api/commit/03fb5b78461d035e14508231ba94a222527d6ee3))

### [4.8.1](https://github.com/VATSIM-UK/uk-controller-api/compare/4.8.0...4.8.1) (2021-09-04)


### Bug Fixes

* **stands:** fix issue with job overlap ([#639](https://github.com/VATSIM-UK/uk-controller-api/issues/639)) ([7731ebd](https://github.com/VATSIM-UK/uk-controller-api/commit/7731ebddfb02a74eca7593f70e942e2bde3069dd)), closes [#637](https://github.com/VATSIM-UK/uk-controller-api/issues/637)


### Data Updates

* **stands:** east midlands stand refresh ([#642](https://github.com/VATSIM-UK/uk-controller-api/issues/642)) ([562c507](https://github.com/VATSIM-UK/uk-controller-api/commit/562c50760d40ed0baf23ba6190405c0ee3d9cabd)), closes [#641](https://github.com/VATSIM-UK/uk-controller-api/issues/641)

## [4.8.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.7.1...4.8.0) (2021-08-21)


### Features

* **stands:** Allow airlines to have prioritised stands ([#625](https://github.com/VATSIM-UK/uk-controller-api/issues/625)) ([a431aa2](https://github.com/VATSIM-UK/uk-controller-api/commit/a431aa2a49e7a09bcc05852861faa11a67eea679))


### Bug Fixes

* **airfield:** fix rounding errors on airfield coordinates ([#626](https://github.com/VATSIM-UK/uk-controller-api/issues/626)) ([b064316](https://github.com/VATSIM-UK/uk-controller-api/commit/b06431611e4737933a2a4f0d9fc22177b50b4d26))
* **networkdata:** data races with timing out aircraft ([#635](https://github.com/VATSIM-UK/uk-controller-api/issues/635)) ([67e348c](https://github.com/VATSIM-UK/uk-controller-api/commit/67e348ca62bb2e109cd3e9ed37f3cb5fe0146e1f)), closes [#634](https://github.com/VATSIM-UK/uk-controller-api/issues/634)


### Data Updates

* **stands:** add heathrow corporate and executive stand assignment ([#623](https://github.com/VATSIM-UK/uk-controller-api/issues/623)) ([d853d64](https://github.com/VATSIM-UK/uk-controller-api/commit/d853d64cbf23589989477f9faa6347ced9c28cd1)), closes [#620](https://github.com/VATSIM-UK/uk-controller-api/issues/620)
* **stands:** add JetBlue Heathrow Stand Assignments ([#622](https://github.com/VATSIM-UK/uk-controller-api/issues/622)) ([9dc36a3](https://github.com/VATSIM-UK/uk-controller-api/commit/9dc36a3c1d784e16593b33d47c4e4c0c0f08c8db)), closes [#621](https://github.com/VATSIM-UK/uk-controller-api/issues/621)
* **stands:** update airline preferences at Stansted ([#636](https://github.com/VATSIM-UK/uk-controller-api/issues/636)) ([f553d9a](https://github.com/VATSIM-UK/uk-controller-api/commit/f553d9a4211eb38d0faeb747a6474fb47704103e)), closes [#627](https://github.com/VATSIM-UK/uk-controller-api/issues/627)

### [4.7.1](https://github.com/VATSIM-UK/uk-controller-api/compare/4.7.0...4.7.1) (2021-08-17)


### Bug Fixes

* **srd:** fix SRD download URL ([#632](https://github.com/VATSIM-UK/uk-controller-api/issues/632)) ([908e2be](https://github.com/VATSIM-UK/uk-controller-api/commit/908e2bef62bfe6b53a9d18a6ccc40142b155ba12)), closes [#631](https://github.com/VATSIM-UK/uk-controller-api/issues/631)

## [4.7.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.6.5...4.7.0) (2021-07-29)


### Features

* Add endpoint to get stands by terminal ([#609](https://github.com/VATSIM-UK/uk-controller-api/issues/609)) ([60b7410](https://github.com/VATSIM-UK/uk-controller-api/commit/60b741063d3e4404a1cdbfab61e7e010ec11d6ae))

### [4.6.5](https://github.com/VATSIM-UK/uk-controller-api/compare/4.6.4...4.6.5) (2021-07-27)


### Bug Fixes

* **network:** fix phantom aircraft caused by network aircraft loop ([#613](https://github.com/VATSIM-UK/uk-controller-api/issues/613)) ([8e640f4](https://github.com/VATSIM-UK/uk-controller-api/commit/8e640f4eb24e5ce905ccd1fc1a26330e9202b65d))

### [4.6.4](https://github.com/VATSIM-UK/uk-controller-api/compare/4.6.3...4.6.4) (2021-07-25)


### Miscellaneous Chores

* **changelog:** Change changelog preset ([d895eaf](https://github.com/VATSIM-UK/uk-controller-api/commit/d895eafa931d324cbe94db3c0c4c1fcd39e03f6d))
* **yarn:** Add missing plugin ([97c4865](https://github.com/VATSIM-UK/uk-controller-api/commit/97c4865f6966413311e1945b8f34fb4588690cf1))

## [4.6.3](https://github.com/VATSIM-UK/uk-controller-api/compare/4.6.2...4.6.3) (2021-07-25)

## [4.6.2](https://github.com/VATSIM-UK/uk-controller-api/compare/4.6.1...4.6.2) (2021-07-25)

## [4.6.1](https://github.com/VATSIM-UK/uk-controller-api/compare/4.6.0...4.6.1) (2021-07-25)

# [4.6.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.5.0...4.6.0) (2021-07-18)


### Features

* **dependencies:** Delete old dependencies ([#604](https://github.com/VATSIM-UK/uk-controller-api/issues/604)) ([ea92a01](https://github.com/VATSIM-UK/uk-controller-api/commit/ea92a011c0b799f7727070bb0feda356df3ca7ed))

# [4.5.0](https://github.com/VATSIM-UK/uk-controller-api/compare/4.4.8...4.5.0) (2021-07-11)


### Features

* **prenotes:** Add the ability to pass prenote messages between plugins ([#593](https://github.com/VATSIM-UK/uk-controller-api/issues/593)) ([bfbb231](https://github.com/VATSIM-UK/uk-controller-api/commit/bfbb2311b93d5f70128aea1848fc4c2324ea3c03))

## [4.4.8](https://github.com/VATSIM-UK/uk-controller-api/compare/4.4.7...4.4.8) (2021-07-02)


### Bug Fixes

* **semantic-release:** Add missing plugin for changelog ([738fdab](https://github.com/VATSIM-UK/uk-controller-api/commit/738fdabbebe79136dbcb08b9f4590bedeecd4886))

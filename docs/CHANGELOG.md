# UK Controller Plugin API Changelog

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

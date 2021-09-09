# UK Controller Plugin API Changelog

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

# Sitegeist.FusionForm.FriendlyCaptcha

This package is for integrating the FriendlyCaptcha into FusionForms. The package provides a field input and matching validator for Fusion.Runtime.Forms. 

### Authors & Sponsors

* Arne Rekemeyer - arne.rekemeyer@sitegeist.de

*The development and the public-releases of this package is generously sponsored
by our employer http://www.sitegeist.de.*

## Installation

Sitegeist.FusionForm.FriendlyCaptcha is available via packagist run `composer require sitegeist/fusionform-friendlycaptcha`.
We use semantic versioning so every breaking change will increase the major-version number.

## Usage

```neosfusion
prototype(Vendor.Site:RuntimeForm) < prototype(Neos.Fusion.Form:Runtime.RuntimeForm) {
    @context {
        showCaptcha = ${this.showCaptcha}
    }

    process {
        content = afx`
            <Sitegeist.FusionForm.FriendlyCaptcha:Scripts @if.1={showCaptcha} />
            ... here be content ...
            <Neos.Fusion.Form:FieldContainer field.name="captchaValidatorField" @if.1={showCaptcha}>
                <Sitegeist.FusionForm.FriendlyCaptcha:Captcha />
            </Neos.Fusion.Form:FieldContainer>
        `

        schema {
            captchaValidatorField = ${Form.Schema.string()}
            captchaValidatorField.@process.captchaValidator = ${value.validator('Sitegeist.FusionForm.FriendlyCaptcha:FriendlyCaptcha')}
            captchaValidatorField.@if.showCaptcha = ${showCaptcha}
        }
    }

    action { ... }
}
```

## Contribution

We will gladly accept contributions. Please send us pull requests.

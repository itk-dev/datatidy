module.exports = {
    "extends": "stylelint-config-recommended-scss",
    "rules": {
        // "color-no-hex": [true, {
        //     "message": "Don't use hex value for colors. Use predefined colors in _bootstrap-custom.scss"
        // }],
        "color-named": ["never", {
            "message": "Don't use named colors. Use predefined colors in _bootstrap-custom.scss"
        }],
        "number-leading-zero": ["never", {
            "message": "For consistency and to save a character don't use leading zeros on values less than 1"
        }],
        "string-quotes": ["double", {
            "message": "For consistency use double quotes around strings"
        }],
        "block-opening-brace-space-before": "always",
        "declaration-block-trailing-semicolon": "always",
        "declaration-block-no-duplicate-properties": [true, {
            ignore: ["consecutive-duplicates-with-different-values"]
        }],
        "declaration-colon-space-after": "always",
        "no-duplicate-selectors": true,
        "indentation": 2
    }
}

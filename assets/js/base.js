require('../scss/base.scss')
require('bootstrap')
// Uncomment this when we need to use jquery
// const $ = require('jquery')

// Add fontawesome
import { library, dom } from '@fortawesome/fontawesome-svg-core';
import { faTachometerAlt } from '@fortawesome/free-solid-svg-icons/faTachometerAlt';
library.add(
    faTachometerAlt
);
dom.watch();

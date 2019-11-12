const $ = require('jquery')
global.$ = global.jQuery = $;

// Add fontawesome
import { library, dom } from '@fortawesome/fontawesome-svg-core'
import { faTachometerAlt } from '@fortawesome/free-solid-svg-icons/faTachometerAlt'
import { faDatabase } from '@fortawesome/free-solid-svg-icons/faDatabase'
require('../scss/base.scss')
require('bootstrap')
library.add(
  faTachometerAlt, faDatabase
)
dom.watch()

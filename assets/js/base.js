// Add fontawesome
import { library, dom } from '@fortawesome/fontawesome-svg-core'
import { faTachometerAlt } from '@fortawesome/free-solid-svg-icons/faTachometerAlt'
import { faDatabase } from '@fortawesome/free-solid-svg-icons/faDatabase'
import { faBolt } from '@fortawesome/free-solid-svg-icons/faBolt'

const $ = require('jquery')
global.$ = global.jQuery = $
require('../scss/base.scss')
require('bootstrap')
library.add(
  faTachometerAlt, faDatabase, faBolt
)
dom.watch()

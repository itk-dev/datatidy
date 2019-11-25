// Add fontawesome
import { library, dom } from '@fortawesome/fontawesome-svg-core'
import { faTachometerAlt } from '@fortawesome/free-solid-svg-icons/faTachometerAlt'
import { faDatabase } from '@fortawesome/free-solid-svg-icons/faDatabase'
import { faBolt } from '@fortawesome/free-solid-svg-icons/faBolt'
import { faUsers } from '@fortawesome/free-solid-svg-icons/faUsers'
import { faSearch } from '@fortawesome/free-solid-svg-icons/faSearch'
import { faEnvelope } from '@fortawesome/free-solid-svg-icons/faEnvelope'
import { faLock } from '@fortawesome/free-solid-svg-icons/faLock'
import { faWaveSquare } from '@fortawesome/free-solid-svg-icons/faWaveSquare'
import { faEdit } from '@fortawesome/free-solid-svg-icons/faEdit'
import { faQuestionCircle } from '@fortawesome/free-solid-svg-icons/faQuestionCircle'
import { faCogs } from '@fortawesome/free-solid-svg-icons/faCogs'
import { faChevronLeft } from '@fortawesome/free-solid-svg-icons/faChevronLeft'
import { faChevronRight } from '@fortawesome/free-solid-svg-icons/faChevronRight'
import { faCog } from '@fortawesome/free-solid-svg-icons/faCog'
import { faFileExport } from '@fortawesome/free-solid-svg-icons/faFileExport'
import { faTrashAlt } from '@fortawesome/free-solid-svg-icons/faTrashAlt'
import { faListUl } from '@fortawesome/free-solid-svg-icons/faListUl'

const $ = require('jquery')
global.$ = global.jQuery = $
require('../scss/base.scss')
require('bootstrap')
library.add(
  faTachometerAlt, faDatabase, faUsers, faSearch, faEnvelope, faLock, faWaveSquare, faEdit, faBolt, faQuestionCircle, faCogs, faChevronLeft, faChevronRight, faCog, faFileExport, faTrashAlt, faListUl
)

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/)
imagesContext.keys().forEach(imagesContext)

dom.watch()

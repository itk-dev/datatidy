// Add fontawesome
import { library, dom } from '@fortawesome/fontawesome-svg-core'
import { faTachometerAlt } from '@fortawesome/pro-light-svg-icons/faTachometerAlt'
import { faDatabase } from '@fortawesome/pro-light-svg-icons/faDatabase'
import { faBolt } from '@fortawesome/pro-light-svg-icons/faBolt'
import { faUsers } from '@fortawesome/pro-light-svg-icons/faUsers'
import { faSearch } from '@fortawesome/pro-light-svg-icons/faSearch'
import { faEnvelope } from '@fortawesome/pro-light-svg-icons/faEnvelope'
import { faLock } from '@fortawesome/pro-light-svg-icons/faLock'
import { faWaveSquare } from '@fortawesome/pro-light-svg-icons/faWaveSquare'
import { faEdit } from '@fortawesome/pro-light-svg-icons/faEdit'
import { faQuestionCircle } from '@fortawesome/pro-light-svg-icons/faQuestionCircle'
import { faCogs } from '@fortawesome/pro-light-svg-icons/faCogs'
import { faChevronLeft } from '@fortawesome/pro-light-svg-icons/faChevronLeft'
import { faChevronRight } from '@fortawesome/pro-light-svg-icons/faChevronRight'
import { faCog } from '@fortawesome/pro-light-svg-icons/faCog'
import { faFileExport } from '@fortawesome/pro-light-svg-icons/faFileExport'
import { faTrashAlt } from '@fortawesome/pro-light-svg-icons/faTrashAlt'
import { faListUl } from '@fortawesome/pro-light-svg-icons/faListUl'
import { faTable } from '@fortawesome/pro-light-svg-icons/faTable'
import { faPlayCircle } from '@fortawesome/free-solid-svg-icons/faPlayCircle'
import { faSpinner } from '@fortawesome/free-solid-svg-icons/faSpinner'
import { faCheckCircle } from '@fortawesome/free-solid-svg-icons/faCheckCircle'
import { faTimesCircle } from '@fortawesome/free-solid-svg-icons/faTimesCircle'
import { faArrowCircleRight } from '@fortawesome/free-solid-svg-icons/faArrowCircleRight'
import { faPlusCircle } from '@fortawesome/free-solid-svg-icons/faPlusCircle'
import { faMinusCircle } from '@fortawesome/free-solid-svg-icons/faMinusCircle'

const $ = require('jquery')
global.$ = global.jQuery = $
require('jquery.are-you-sure')

require('../scss/base.scss')
require('bootstrap')
library.add(
  faTachometerAlt,
  faDatabase,
  faUsers,
  faSearch,
  faEnvelope,
  faLock,
  faWaveSquare,
  faEdit,
  faBolt,
  faQuestionCircle,
  faCogs,
  faChevronLeft,
  faChevronRight,
  faCog,
  faFileExport,
  faTrashAlt,
  faListUl,
  faTable,
  faPlayCircle,
  faSpinner,
  faCheckCircle,
  faTimesCircle,
  faArrowCircleRight,
  faPlusCircle,
  faMinusCircle
)

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/)
imagesContext.keys().forEach(imagesContext)

dom.watch()

$(function () {
  $('[data-toggle="tooltip"]').tooltip()

  $('form').areYouSure()
})

$('#searchModal').on('shown.bs.modal', function () {
  $('#search-input').trigger('focus')
})

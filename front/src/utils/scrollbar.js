function getScrollbarWidth () {
  const outer = document.createElement('div')
  outer.style.visibility = 'hidden'
  outer.style.overflow = 'scroll' // @ts-ignore
  outer.style.msOverflowStyle = 'scrollbar'
  document.body.appendChild(outer)
  const inner = document.createElement('div')
  outer.appendChild(inner)
  const scrollbarWidth = (outer.offsetWidth - inner.offsetWidth)
  outer.parentNode?.removeChild(outer)
  return scrollbarWidth
}

function removeScrollBar () {
  const html = document.getElementsByTagName('html')[0]
  const layout = document.getElementById('layout')
  const navbar = document.getElementById('navbar')
  html.style.overflow = 'hidden'
  if (layout) {
    layout.style.marginRight = getScrollbarWidth() + 'px'
  }
  if (navbar) {
    navbar.style.paddingRight = getScrollbarWidth() + 'px'
  }
}

function addScrollBar () {
  const html = document.getElementsByTagName('html')[0]
  const layout = document.getElementById('layout')
  const navbar = document.getElementById('navbar')
  html.style.overflow = ''
  if (layout) {
    layout.style.marginRight = ''
  }
  if (navbar) {
    navbar.style.paddingRight = ''
  }
}

export default { getScrollbarWidth, removeScrollBar, addScrollBar }

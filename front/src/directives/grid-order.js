const sort = new DOMParser().parseFromString('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sort" class="svg-inline--fa fa-sort fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41zm255-105L177 64c-9.4-9.4-24.6-9.4-33.9 0L24 183c-15.1 15.1-4.4 41 17 41h238c21.4 0 32.1-25.9 17-41z"></path></svg>', 'text/xml').documentElement
const up = new DOMParser().parseFromString('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sort-up" class="svg-inline--fa fa-sort-up fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279 224H41c-21.4 0-32.1-25.9-17-41L143 64c9.4-9.4 24.6-9.4 33.9 0l119 119c15.2 15.1 4.5 41-16.9 41z"></path></svg>', 'text/xml').documentElement
const down = new DOMParser().parseFromString('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sort-down" class="svg-inline--fa fa-sort-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41z"></path></svg>', 'text/xml').documentElement

function bindGridOrder (el, binding) {
  if (el.gridOrderLoaded === true) {
    return
  }

  const columns = Array.from(el.querySelector('tr').children)
  if (columns.length <= 1) {
    return
  }

  el.gridOrderLoaded = true

  columns.forEach(item => {
    if (item.getAttribute('data-column')) {
      item.classList.add('grid-sortable')

      const sortIcon = sort.cloneNode(true)
      const upIcon = up.cloneNode(true)
      const downIcon = down.cloneNode(true)

      item.insertAdjacentElement('afterbegin', sortIcon)
      item.insertAdjacentElement('afterbegin', upIcon)
      item.insertAdjacentElement('afterbegin', downIcon)

      item.addEventListener('click', (e) => orderBy(e, el))
    }
  })
}

const gridOrder = {
  update: bindGridOrder,
  bind: bindGridOrder
}

function orderBy (e, el) {
  const target = e.target

  if (!target.classList.contains('sort-up')) {
    const sorted = target.parentElement.querySelector('.sort-up')
    if (sorted) {
      sorted.classList.remove('sort-up', 'sort-down')
    }

    target.classList.add('sort-up')

    const event = new CustomEvent('sort', {
      detail: {order: target.getAttribute('data-column'), direcao: 'ASC'}
    })
    el.dispatchEvent(event)
    return
  }

  if (!target.classList.contains('sort-down')) {
    target.classList.add('sort-down')
    const event = new CustomEvent('sort', {
      detail: {order: target.getAttribute('data-column'), direcao: 'DESC'}
    })

    el.dispatchEvent(event)
    return
  }

  target.classList.remove('sort-up', 'sort-down')

  const event = new CustomEvent('sort', {
    detail: {order: null, direcao: ''}
  })
  el.dispatchEvent(event)
}

export default gridOrder

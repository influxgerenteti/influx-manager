const teclasLiberadas = [
  'Backspace',
  'Enter',
  'Delete',
  'Shift',
  'Tab',
  'ArrowLeft',
  'ArrowRight',
  'ArrowDown',
  'ArrowUp'
]

export default {
  bind (el) {
    el.addEventListener('keydown', $event => {
      if (/\d/.test($event.key) === false && teclasLiberadas.includes($event.key) === false) {
        $event.preventDefault()
      }
    })
  }
}

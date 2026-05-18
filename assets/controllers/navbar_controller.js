/*
  Stimulus controller voor het burger-menu
  - Klik op burger togglet menu zichtbaar/niet zichtbaar
*/
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["menu", "burger"]

    connect() {
        // Zorg dat menu en burger aanwezig zijn
        if (!this.hasMenuTarget || !this.hasBurgerTarget) return

        this.burgerTarget.addEventListener('click', () => {
            this.menuTarget.classList.toggle('is-active')
            this.burgerTarget.classList.toggle('is-active')
        })
    }
}
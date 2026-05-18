import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["menu", "burger"]

    connect() {
        if (!this.hasMenuTarget || !this.hasBurgerTarget) return

        // Start met sidebar verborgen op mobiel
        this.menuTarget.classList.add('is-hidden-mobile')

        // Burger klik togglet zichtbaarheid
        this.burgerTarget.addEventListener('click', () => {
            this.menuTarget.classList.toggle('is-hidden-mobile')
        })
    }
}
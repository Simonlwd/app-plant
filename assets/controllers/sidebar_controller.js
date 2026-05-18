import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["burger", "menu", "overlay"]

    connect() {
        this.burgerTarget.addEventListener("click", () => {
            this.toggle()
        })

        this.overlayTarget.addEventListener("click", () => {
            this.close()
        })
    }

    toggle() {
        this.menuTarget.classList.toggle("is-open")
        this.overlayTarget.classList.toggle("is-active")
    }

    close() {
        this.menuTarget.classList.remove("is-open")
        this.overlayTarget.classList.remove("is-active")
    }
}
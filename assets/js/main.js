document.addEventListener("DOMContentLoaded", () => {
  // limpiar chat al cargar la página
const chatBox = document.getElementById("chat-messages");
if (chatBox) chatBox.innerHTML = "";

  // Mobile menu toggle
  const mobileMenuButton = document.getElementById("mobile-menu-button");
  const mobileMenu = document.getElementById("mobile-menu");

  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener("click", () => {
      mobileMenu.classList.toggle("open");
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll("#mobile-menu a").forEach((link) => {
      link.addEventListener("click", () => {
        mobileMenu.classList.remove("open");
      });
    });
  }

  // Smooth scrolling for navigation links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (!href || href === "#") return;

      const target = document.querySelector(href);
      if (!target) return;

      e.preventDefault();
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });

  // ============================
  // CONTACT FORM (real backend)
  // ============================
  const contactForm = document.getElementById("contact-form");
  const contactStatus = document.getElementById("contact-status");

  function showContactStatus(message, isError) {
    if (!contactStatus) return;
    contactStatus.textContent = message;
    contactStatus.classList.remove("hidden");
    contactStatus.classList.toggle("text-red-600", !!isError);
    contactStatus.classList.toggle("text-green-600", !isError);
  }

  if (contactForm) {
    contactForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const submitBtn = contactForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      showContactStatus("Enviando...", false);

      try {
        const formData = new FormData(contactForm);

        const res = await fetch(contactForm.action, {
          method: "POST",
          body: formData,
          headers: {
            Accept: "application/json",
            "X-Requested-With": "fetch",
          },
        });

        const data = await res.json().catch(() => null);

        if (!res.ok || !data || data.ok !== true) {
          const msg = data?.message || "No se pudo enviar el mensaje.";
          showContactStatus(msg, true);
          return;
        }

        showContactStatus(data.message || "Mensaje enviado correctamente.", false);
        contactForm.reset();
      } catch (err) {
        console.error("Error enviando formulario:", err);
        showContactStatus("Hubo un error. Intenta nuevamente.", true);
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  // Add active state to navigation on scroll
  window.addEventListener("scroll", () => {
    const sections = document.querySelectorAll("section[id]");
    const navLinks = document.querySelectorAll('nav a[href^="#"]');

    let current = "";
    sections.forEach((section) => {
      const sectionTop = section.offsetTop - 100;
      if (window.scrollY >= sectionTop) current = section.getAttribute("id") || "";
    });

    navLinks.forEach((link) => {
      link.classList.remove("text-primary");
      if (link.getAttribute("href") === `#${current}`) link.classList.add("text-primary");
    });
  });

  // ============================
  // CHAT WIDGET (simple MVP)
  // ============================
  const chatWidget = document.getElementById("chat-widget");
  const chatToggle = document.getElementById("chat-toggle");
  const chatClose = document.getElementById("chat-close");
  const chatInput = document.getElementById("chat-input");
  const chatSend = document.getElementById("chat-send");
  const chatMessages = document.getElementById("chat-messages");

  if (chatWidget && chatToggle && chatClose && chatInput && chatSend && chatMessages) {
    chatWidget.style.display = "none";

    chatToggle.addEventListener("click", () => {
    chatWidget.style.display = "block";
    chatToggle.style.display = "none";
    chatMessages.innerHTML = "";   // chat en blanco al abrir
    chatInput.focus();
  });

    chatClose.addEventListener("click", () => {
      chatWidget.style.display = "none";
      chatToggle.style.display = "block";
    });

    // click enviar (robusto aunque algún día esté dentro de un form)
    chatSend.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      sendMessage();
    });

    // enter enviar
    chatInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        e.stopPropagation();
        sendMessage();
      }
    });

    async function sendMessage() {
      const text = chatInput.value.trim();
      if (!text) return;

      // feedback inmediato (optimista)
      appendMessage({ sender: "visitor", content: text });
      chatInput.value = "";

      try {
        await fetch("php/chat/send.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ sender: "visitor", content: text }),
      });

      // ✅ Auto-respuesta SOLO en el frontend (sin cargar historial)
      appendMessage({
        sender: "bot",
        content: "✅ Recibí tu mensaje. Para poder responderte, por favor completá el formulario con tu nombre y tu email y nos pondremos en contacto cuanto antes. 👉 Ir al formulario: #contact"
      });
      } catch (err) {
        console.error("Error enviando mensaje:", err);
        appendMessage({
          sender: "bot",
          content: "No pude enviar el mensaje. Verifica que Apache/MySQL estén activos y recarga la página.",
        });
      }
    }

    function appendMessage(msg) {
  const div = document.createElement("div");

  div.className =
    msg.sender === "visitor"
      ? "bg-blue-500 text-white rounded-xl px-4 py-2 w-max ml-auto max-w-[85%]"
      : "bg-gray-100 rounded-xl px-4 py-2 w-max max-w-[85%]";

  div.textContent = msg.content || "";

  // ✅ Si el bot incluye "#contact", el mensaje se vuelve clickeable y hace scroll al formulario
  if (msg.sender !== "visitor" && (msg.content || "").includes("#contact")) {
    div.style.cursor = "pointer";
    div.title = "Ir al formulario de contacto";
    div.addEventListener("click", () => {
      const target = document.querySelector("#contact");
      if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });

      // Opcional: enfocar el primer input del formulario (si existe)
      const nameInput = document.querySelector('#contact-form input[name="nombre"]');
      if (nameInput) nameInput.focus();
    });
  }

  chatMessages.appendChild(div);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

    async function loadMessages() {
      try {
        const res = await fetch("php/chat/get.php", { cache: "no-store" });
        const data = await res.json().catch(() => []);

        chatMessages.innerHTML = "";
        (Array.isArray(data) ? data : []).forEach((msg) => appendMessage(msg));
      } catch (err) {
        console.error("Error cargando mensajes:", err);
      }
    }
  }
});



  const toggle = document.getElementById("themeToggle");
  const label  = document.getElementById("themeLabel");

  // Load saved theme
  const savedTheme = localStorage.getItem("theme");

  if (savedTheme === "light") {
    document.body.classList.add("light-theme");
    toggle.checked = true;
    label.innerText = "Light Mode";
  } else {
    label.innerText = "Dark Mode";
  }

  // Toggle behaviour
  toggle.addEventListener("change", () => {
    if (toggle.checked) {
      document.body.classList.add("light-theme");
      localStorage.setItem("theme", "light");
      label.innerText = "Light Mode";
    } else {
      document.body.classList.remove("light-theme");
      localStorage.setItem("theme", "dark");
      label.innerText = "Dark Mode";
    }
  });

  const form = document.getElementById('suggestionForm');
    const successMessage = document.getElementById('successMessage');

    form.addEventListener('submit', function(event) {
      event.preventDefault(); // stop reload

      
      const formData = {
        name: form.name.value,
        email: form.email.value,
        type: form.type.value,
        message: form.message.value
      };

      console.log('Suggestion submitted:', formData);

      // Temporary success message
      successMessage.style.display = 'block';
      form.reset();

      // Hide message after 4 seconds
      setTimeout(() => {
        successMessage.style.display = 'none';
      }, 4000);
    });




    


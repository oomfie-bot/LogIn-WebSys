const validation = new JustValidate("#signupForm");

validation
  .addField("#name", [
    {
      rule: "required",
      errorMessage: "Name is required"
    }
  ])
  .addField("#email", [
    {
      rule: "required",
      errorMessage: "Email is required"
    },
    {
      rule: "email",
      errorMessage: "Email is invalid"
    },
    {
      validator: (value) => {
        if (!value) {
          return true; // Skip validation if the field is empty
        }
        return fetch("validate-email.php?email=" + encodeURIComponent(value))
          .then(function(response) {
            return response.json();
          })
          .then(function(json) {
            return json.available;
          });
      },
      errorMessage: "Email already taken"
    }
  ])
  .addField("#password", [
    {
      rule: "required",
      errorMessage: "Password is required"
    },
    {
      rule: "password",
      errorMessage: "Password is invalid"
    }
  ])
  .addField("#confirm-password", [
    {
      validator: (value, fields) => {
        return value === fields["#password"].elem.value;
      },
      errorMessage: "Passwords should match"
    }
  ])
  .onSuccess((event) => {
    document.getElementById("signupForm").submit();
  });
<template>
  <div>
    <h1 v-if="!isAuthenticated">Zaloguj się lub Zarejestruj</h1>
    <div v-if="!isAuthenticated">
      <input v-model="user.username" placeholder="Login" />
      <input v-model="user.password" type="password" placeholder="Hasło" />
      <button @click="loginOrRegister">Zaloguj</button>
      <div v-if="needRegistration">
        <input v-model="user.firstname" placeholder="Imię">
        <input v-model="user.lastname" placeholder="Nazwisko">
        <button @click="register">Zarejestruj</button>
      </div>
    </div>
    <div v-else>
      <h1>Dodaj nowy post</h1>
      <form @submit.prevent="handlePost">
        <input v-model="newPost.title" placeholder="Tytuł posta" />
        <textarea v-model="newPost.content" placeholder="Treść posta"></textarea>
        <button type="submit">Dodaj Post</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {
        username: "",
        password: "",
        firstname: "",
        lastname: ""
      },
      newPost: {
        title: "",
        content: ""
      },
      isAuthenticated: this.checkAuthentication(),
      needRegistration: false,
    };
  },
  methods: {
    loginOrRegister() {
      fetch('http://localhost/skrypty/verifyUser.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username: this.user.username, password: this.user.password }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.message) {
          this.isAuthenticated = true;
          localStorage.setItem('user_id', data.user_id);
          alert('Zalogowano pomyślnie.');
        } else if (data.need_registration) {
          this.needRegistration = true;
          alert('Użytkownik nie istnieje. Proszę się zarejestrować.');
        } else if (data.error) {
          alert(data.error);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("Błąd sieciowy.");
      });
    },
    register() {
      const postUser = {
        username: this.user.username,
        password: this.user.password,
        firstname: this.user.firstname,
        lastname: this.user.lastname,
        action: "register"
      };

      fetch('http://localhost/skrypty/verifyUser.php?action=register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(postUser),
      })
      .then(response => response.json())
      .then(data => {
        if (data.message) {
          alert('Użytkownik dodany pomyślnie');
          this.user.firstname = '';
          this.user.lastname = '';
          this.isAuthenticated = true;
          localStorage.setItem('user_id', data.user_id);
          this.needRegistration = false;
        } else {
          alert("Błąd: " + data.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert("Błąd sieciowy.");
      });
    },
    handlePost() {
      const postData = {
        title: this.newPost.title,
        content: this.newPost.content,
        user_id: localStorage.getItem('user_id'),
      };

      fetch('http://localhost/skrypty/fetchData.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(postData),
      })
      .then(response => response.json())
      .then(data => {
        if (data.message) {
          alert('Post dodany pomyślnie');
          this.newPost.title = '';
          this.newPost.content = '';
          this.$router.push('/');
        } else {
          alert("Błąd: " + data.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert("Błąd sieciowy.");
      });
    },
    checkAuthentication() {
      return localStorage.getItem('user_id') !== null; // Sprawdzamy Local Storage
    }
  }
};
</script>

<style scoped>
div {
  max-width: 500px;
  margin: 50px auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  font-family: "Helvetica", "Arial", sans-serif;
}

h1 {
  text-align: center;
  color: #333;
}

input,
textarea {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: 2px solid #ccc;
  border-radius: 4px;
  resize: none;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #4caf50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 20px;
  font-size: 16px;
}

button:hover {
  background-color: #45a049;
}
</style>

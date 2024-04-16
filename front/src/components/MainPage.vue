<template>
  <div>
    <h2>Posty</h2>
    <div class="guzik">
      <button @click="goToAddPostPage">Dodaj post</button>
    </div>
    <div v-if="posts.length">
      <div class="post-container" v-for="post in posts" :key="post.id">
        <h3>{{ post.title }}</h3>
        <p>{{ post.content }}</p>
        <p class="user">{{ post.firstname }} {{ post.lastname }}</p>
        <button @click="deletePost(post.id)">Usuń post</button>
      </div>
    </div>
    <p class="blad" v-else>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        fill="currentColor"
        class="bi bi-question-octagon"
        viewBox="0 0 16 16"
        style="margin-right: 8px"
      >
        <path
          d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1z"
        />
        <path
          d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"
        />
      </svg>
      Brak postów do wyświetlenia.
    </p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      posts: [],
      newPost: {
        title: "",
        content: "",
      },
      isAuthenticated: this.checkAuthentication(), // Zmieniamy na sprawdzenie Local Storage
    };
  },
  methods: {
    fetchPosts() {
      fetch("http://localhost/skrypty/fetchData.php")
        .then((response) => {
          if (!response.ok) {
            throw new Error("Odpowiedź sieci nie jest poprawna" + response.statusText);
          }
          return response.json();
        })
        .then((data) => {
          if(!data.posts) {
            alert("Nie znaleziono postów!");
          } else {
            this.posts = data.posts;
          }
        })
        .catch((error) => {
          console.error("Wystąpił błąd podczas pobierania postów: ", error);
        });
    },
    deletePost(id) {
      fetch(`http://localhost/skrypty/fetchData.php?id=${id}`, {
        method: "DELETE",
      })
      .then((response) => response.json())
      .then((data) => {
        console.log(data);
        this.fetchPosts();
      })
      .catch((error) => console.error("Error:", error));
    },
    goToAddPostPage() {
      this.$router.push("/add-post");
    },
    checkAuthentication() {
      return localStorage.getItem('user_id') !== null; // Używamy Local Storage do sprawdzenia autoryzacji
    }
  },
  mounted() {
    this.fetchPosts();
  }
};
</script>

<style scoped>
* {
  font-family: Arial, Helvetica, sans-serif;
}

body {
  background-color: #e1e1e1 !important;
}

h2 {
  color: #000000;
  font-family: "Raleway", sans-serif;
  font-size: 62px;
  font-weight: 800;
  line-height: 72px;
  margin: 0 0 24px;
  text-align: center;
  text-transform: uppercase;
  text-align: center;
}

.post-container {
  max-width: 600px;
  position: relative;
  margin: 20px auto;
  padding: 20px;
  background-color: #f0f4f8;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px,
    rgba(0, 0, 0, 0.22) 0px 10px 10px;
  overflow: auto;
}

.post-container button {
  font-size: 12px;
  padding: 3px 7px;
  background-image: linear-gradient(to right, #ff4757 0%, #fa6874 100%);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.user {
  position: absolute;
  top: 10px;
  right: 10px;
  color: #555;
  font-size: 0.85em;
}

p.blad {
  display: flex;
  justify-content: center;
  font-weight: 550;
  max-width: 600px;
  margin: 20px auto;
  padding: 20px;
  background-color: #f0f4f8;
  color: #cc0000;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px,
    rgba(0, 0, 0, 0.22) 0px 10px 10px;
}

.post-operations {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 300px;
  margin: 20px;
  padding: 15px;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: absolute;
  top: 0;
  left: 0;
}

.post-operations input,
.post-operations textarea {
  width: 100%;
  padding: 8px;
  margin-bottom: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
}

.post-operations textarea {
  height: 80px;
  resize: none;
}

.post-operations button {
  padding: 8px 16px;
  background-color: #5c67f2;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.post-operations button:hover {
  background-color: #4a54e1;
}

.guzik {
  margin: 20px auto;
  display: flex;
  justify-content: center;
}

.guzik button {
  padding: 8px 16px;
  background-color: #5c67f2;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.guzik button:hover {
  background-color: #4a54e1;
}

h3 {
  font-weight: bold;
}
</style>

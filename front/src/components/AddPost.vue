<template>
  <div>
    <h1>Dodaj nowy post</h1>
    <form @submit.prevent="addPost">
      <input v-model="newPost.title" placeholder="Tytuł posta" />
      <textarea v-model="newPost.content" placeholder="Treść posta"></textarea>
      <button type="submit">Dodaj Post</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      newPost: {
        title: '',
        content: ''
      }
    };
  },

  methods: {
    addPost() {
      const postData = {
        title: this.newPost.title,
        content: this.newPost.content
      };

      fetch('http://localhost/skrypty/fetchData.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(postData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.message) {
          this.$router.push('/');
        } else {
          alert("Błąd: " + data.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert("Błąd sieciowy.");
      });
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
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  font-family: 'Helvetica', 'Arial', sans-serif;
}

h1 {
  text-align: center;
  color: #333;
}

input, textarea {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: 2px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #4CAF50;
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


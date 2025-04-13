// Utility function to fetch user data
async function fetchUserData(username) {
    const response = await fetch(`/api/user/${username}`);
    return await response.json();
  }
  
  // Utility function to display workouts
  function displayWorkouts(workouts, container) {
    container.innerHTML = '';
    workouts.forEach(workout => {
      const div = document.createElement('div');
      div.innerHTML = `<p><strong>${new Date(workout.date).toLocaleDateString()}</strong></p>`;
      workout.exercises.forEach(ex => {
        div.innerHTML += `<p>${ex.name}: ${ex.sets} sets, ${ex.reps} reps, ${ex.weight}kg</p>`;
      });
      container.appendChild(div);
    });
  }
  
  // Utility function to display progress
  function displayProgress(progress, container) {
    container.innerHTML = '';
    progress.forEach(p => {
      const div = document.createElement('div');
      div.innerHTML = `<p>${new Date(p.date).toLocaleDateString()}: Weight ${p.weight}kg, Body Fat ${p.bodyFat}%</p>`;
      container.appendChild(div);
    });
  }
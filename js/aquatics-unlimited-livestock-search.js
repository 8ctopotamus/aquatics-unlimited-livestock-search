(function() {
  const loading = document.getElementById('loading')
  const form = document.getElementById('au-search')
  const resultsList = document.getElementById('au-search-results')
  const startingCategories = resultsList.innerHTML
  const resetButton = document.getElementById('reset-au-search-results')

  function showLoading() {
    loading.style.display = 'block'
  }

  function hideLoading() {
    loading.style.display = 'none'
  }

  function resetResults() {
    resultsList.innerHTML = startingCategories
  }

  function renderTemplate(obj) {
    resultsList.innerHTML += `<li>
      <a href="${obj.permalink}">
        <img class="livestock-thumbnail" src="${obj.thumbnail}" alt="${obj.title}" />
        <span class="livestock-title">${obj.title}</span>
      </a>
    </li>`
  }

  function renderResults(json) {
    if (json.length > 0) {
      resultsList.innerHTML = ''
      json.forEach(obj => renderTemplate(obj))
    } else {
      results.innerHTML = '<li>No matches found.</li>'
    }
  }

  async function handleFormSubmit(e) {
    e.preventDefault()
    showLoading()
    const data = {
			'action': 'au_fetch_livestock',
			'cat': 18
		}
    let form_data = new FormData()
    for (key in data) {
      form_data.append(key, data[key])
    }
    try {
      const response = await fetch(wp_data.ajax_url, {
        method: 'POST',
        body: form_data
      })
      const json = await response.json()
      renderResults(json)
    } catch (err) {
      throw new Error(err)
    }
    hideLoading()
  }

  form.addEventListener('submit', handleFormSubmit)
  resetButton.addEventListener('click', resetResults)
  hideLoading()
})()

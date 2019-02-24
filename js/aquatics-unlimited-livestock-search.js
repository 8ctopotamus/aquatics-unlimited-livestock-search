(function() {
  const loading = document.getElementById('loading')
  const catSelectors = document.getElementsByClassName('catSelector')
  const form = document.getElementById('au-search-form')
  const fields = document.getElementById('au-search-fields')
  const resultsList = document.getElementById('au-search-results')
  const initialCats = resultsList.innerHTML
  const resetButton = document.getElementById('reset-au-search-results')

  function showLoading() {
    loading.style.display = 'block'
  }

  function hideLoading() {
    loading.style.display = 'none'
  }

  function resetResults() {
    resultsList.innerHTML = initialCats
    Object.values(catSelectors).forEach(cat => cat.addEventListener('click', searchCategory))
    form.style.display = 'none'
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
      Object.values(catSelectors).forEach(cat => cat.removeEventListener('click', searchCategory))
      form.style.display = 'block'
      resultsList.innerHTML = ''
      json.forEach(obj => renderTemplate(obj))
    } else {
      form.style.display = 'block'
      resultsList.innerHTML = '<li>No matches found.</li>'
    }
  }

  async function handleFormSubmit(e) {
    e.preventDefault()
    showLoading()
    console.log('form submit')
    hideLoading()
  }

  async function searchCategory(e) {
    e.preventDefault()
    showLoading()
    const target = e.target
    const a = target.tagName === 'A' ? target : target.parentNode
    const catId = a.dataset.catid
    const data = {
      action: 'au_fetch_livestock',
      cat: catId
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
      console.log(json)
      renderResults(json)
    } catch (err) {
      throw new Error(err)
    }
    hideLoading()
  }

  form.addEventListener('submit', handleFormSubmit)
  Object.values(catSelectors).forEach(cat => cat.addEventListener('click', searchCategory))
  resetButton.addEventListener('click', resetResults)
  hideLoading()
})()

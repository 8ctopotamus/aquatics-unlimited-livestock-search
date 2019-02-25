(function() {
  const loading = document.getElementById('loading')
  const catSelectors = document.getElementsByClassName('catSelector')
  const searchForm = document.getElementById('au-search-form')
  const resultsList = document.getElementById('au-search-results')
  const initialCats = resultsList.innerHTML
  const resetButton = document.getElementById('reset-au-search-results')

  let postsPerPage = 12
  let paged = 0
  let cat = false

  function showLoading() {
    loading.style.display = 'flex'
  }

  function hideLoading() {
    loading.style.display = 'none'
  }

  function resetResults() {
    paged = 0
    cat = false
    resultsList.innerHTML = initialCats
    Object.values(catSelectors).forEach(cat => cat.addEventListener('click', searchCategory))
    searchForm.style.display = 'none'
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
    if (json.data.length > 0) {
      Object.values(catSelectors).forEach(cat => cat.removeEventListener('click', searchCategory))
      searchForm.style.display = 'block'
      resultsList.innerHTML = ''
      json.data.forEach(obj => renderTemplate(obj))
    } else {
      searchForm.style.display = 'block' // to show reset button
      resultsList.innerHTML = `<li>${json.total} matches found.</li>`
    }
  }

  function renderFields(data) {
    console.log(data)
  }

  async function searchFormSubmit(e) {
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
    if (a.dataset.catid) {
      cat = a.dataset.catid
    }
    const data = {
      action: 'au_fetch_livestock',
      cat,
      postsPerPage,
      paged
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
      renderFields(json.data)
      renderResults(json)
    } catch (err) {
      throw new Error(err)
    }
    hideLoading()
  }

  searchForm.addEventListener('submit', searchFormSubmit)
  resetButton.addEventListener('click', resetResults)
  Object.values(catSelectors).forEach(cat => cat.addEventListener('click', searchCategory))

  hideLoading()
})()

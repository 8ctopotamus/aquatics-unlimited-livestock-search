(function() {
  const loading = document.getElementById('loading')
  const searchForm = document.getElementById('au-search-form')
  const resultsStatsContainer = document.getElementById('results-stats-container')
  const resultsStats = document.getElementById('results-stats')
  const resultsList = document.getElementById('au-search-results-grid')
  const initialCats = resultsList.innerHTML
  const initialCatsSelectors = document.getElementsByClassName('catSelector')
  const resetButton = document.getElementById('reset-au-search-results')

  let postsPerPage = 12
  let paged = 0
  let cat = false
  let catName = ''

  const showLoading = () => {
    loading.classList.add('loading-shown')
  }

  const hideLoading = () => {
    loading.classList.remove('loading-shown')
  }

  const showSearchUI = () => {
    searchForm.style.display = 'block'
    resultsStatsContainer.style.display = 'flex'
  }

  const hideSearchIU = () => {
    searchForm.style.display = 'none'
    resultsStats.innerHTML = ''
    resultsStatsContainer.style.display = 'none'
  }

  const resetResults = () => {
    paged = 0
    cat = false
    catName = ''
    resultsList.innerHTML = initialCats
    Object.values(initialCatsSelectors).forEach(cat => cat.addEventListener('click', searchCategory))
    hideSearchIU()
  }

  const renderTemplate = obj => {
    resultsList.innerHTML += `<li>
      <a href="${obj.permalink}">
        <img class="livestock-thumbnail" src="${obj.thumbnail}" alt="${obj.title}" />
        <span class="livestock-title">${obj.title}</span>
      </a>
    </li>`
  }

  const renderResults = json => {
    const { data, total } = json
    resultsStats.innerHTML = `<h2>${catName} <small>${total} matches found.</small></h2>`
    resultsList.innerHTML = ''
    if (data.length > 0) {
      Object.values(initialCatsSelectors).forEach(cat => cat.removeEventListener('click', searchCategory))
      data.forEach(obj => renderTemplate(obj))
    }
    showSearchUI()
  }

  const searchFormSubmit = async (e) => {
    e.preventDefault()
    showLoading()
    console.log('form submit')
    hideLoading()
  }

  const searchCategory = async (e) => {
    e.preventDefault()
    showLoading()
    const target = e.target
    const a = target.tagName === 'A' ? target : target.parentNode
    if (a.dataset.catid) {
      cat = a.dataset.catid
    }
    if (a.dataset.catname) {
      catName = a.dataset.catname
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
      renderResults(json)
    } catch (err) {
      alert(`😵 ${err}`)
      throw new Error(err)
    }
    hideLoading()
  }

  searchForm.addEventListener('submit', searchFormSubmit)
  resetButton.addEventListener('click', resetResults)
  Object.values(initialCatsSelectors).forEach(cat => cat.addEventListener('click', searchCategory))

  hideLoading()
})()

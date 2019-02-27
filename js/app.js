(function() {
  const loading = document.getElementById('loading')
  const searchForm = document.getElementById('au-search-form')
  const resultsStatsContainer = document.getElementById('results-stats-container')
  const resultsStats = document.getElementById('results-stats')
  const resultsList = document.getElementById('au-search-results-grid')
  const initialCats = resultsList.innerHTML
  const initialCatsSelectors = document.getElementsByClassName('catSelector')
  const resetButton = document.getElementById('reset-au-search-results')
  const animationDuraton = 260
  const debug = false // for devs

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
    searchForm.classList.add('rotate-in-3d')
    resultsStatsContainer.style.display = 'flex'
  }

  const hideSearchIU = () => {
    searchForm.style.display = 'none'
    searchForm.classList.remove('rotate-in-3d')
    resultsStats.innerHTML = ''
    resultsStatsContainer.style.display = 'none'
  }

  const reset = () => {
    paged = 0
    cat = false
    catName = ''
    resultsList.innerHTML = initialCats
    Object.values(initialCatsSelectors).forEach(cat => {
      cat.addEventListener('click', searchCategory)
      cat.classList.remove('fade-out')
      cat.classList.add('fade-in')
    })
    hideSearchIU()
  }

  const renderThumbnail = obj => {
    resultsList.innerHTML += `<li>
      <a href="${obj.permalink}" class="fade in">
        <img class="livestock-thumbnail" src="${obj.thumbnail}" alt="${obj.title}" />
        <span class="livestock-title">${obj.title}</span>
      </a>
    </li>`
  }

  const renderResults = json => {
    const { data, total, debug } = json
    if (debug) {
      console.info('Debug', debug)
    }
    if (data.length > 0) {
      Object.values(initialCatsSelectors).forEach(cat => {
        cat.removeEventListener('click', searchCategory)
        cat.classList.add('fade-out')
      })
      setTimeout(() => {
        let displaying = postsPerPage
        if (total <= postsPerPage) {
          displaying = total
        }
        resultsStats.innerHTML = `<h2>${catName} <small>Displaying ${displaying} / ${total} matches found.</small></h2>`
        resultsList.innerHTML = ''
        data.forEach(obj => renderThumbnail(obj))
        showSearchUI()
      }, animationDuraton)
    }
  }

  const fetchLivestock = async form_data => {
    try {
      const response = await fetch(wp_data.ajax_url, {
        method: 'POST',
        body: form_data
      })
      const json = await response.json()
      renderResults(json)
      hideLoading()
    } catch (err) {
      hideLoading()
      alert(`😵 ${err}`)
      throw new Error(err)
    }
  }

  const formSubmit = e => {
    e.preventDefault()
    showLoading()
    let form_data = new FormData(searchForm)
    const data = {
      action: 'au_fetch_livestock',
      includeMeta: true,
      cat,
      postsPerPage,
      paged,
      debug,
    }
    for (key in data) {
      form_data.append(key, data[key])
    }
    fetchLivestock(form_data)
  }

  const searchCategory = e => {
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
      paged,
      debug
    }
    let form_data = new FormData()
    for (key in data) {
      form_data.append(key, data[key])
    }
    fetchLivestock(form_data)
  }

  searchForm.addEventListener('submit', formSubmit)
  resetButton.addEventListener('click', reset)
  Object.values(initialCatsSelectors).forEach(cat => cat.addEventListener('click', searchCategory))

  hideLoading()
})()

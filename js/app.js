(function() {
  const { ajax_url, cats_array, plugin_slug } = wp_data
  const loading = document.getElementById('loading')
  const searchForm = document.getElementById(plugin_slug + '-form')
  const searchFormSubmitButton = document.getElementById(plugin_slug + '-form-submit')
  const searchFormSelects = document.getElementsByClassName('au-select')
  const resultsStatsContainer = document.getElementById('results-stats-container')
  
  const resultsStats = document.getElementById('results-stats')

  const resultsList = document.getElementById('au-search-results-grid')
  const pageCount = document.getElementById('page-count')
  const paginationButtons = document.getElementsByClassName('au-pagination-button')
  const initialCats = resultsList.innerHTML
  const initialCatsSelectors = document.getElementsByClassName('catSelector')
  const resetButton = document.getElementById('reset-au-search-results')
  const localStorageKey = 'au_search_params'
  const animationDuraton = 260

  // get query params, if supplied
  const queryParams = new URLSearchParams(window.location.search)
  
  let shouldReset = JSON.parse(queryParams.get("reset")) 
    || document.getElementsByTagName("BODY")[0].classList.contains('home')

  let catId = queryParams.get('catId')
  if (catId) {
    catId = JSON.parse(catId)
  }

  let totalResults = 0

  let params = {
    action: 'au_fetch_livestock',
    includeMeta: false,
    cat: false,
    postsPerPage: 12,
    paged: 1,
    debug: false // for devs
  }

  let searchCriteria = []

  const showLoading = () => {
    loading.classList.add('loading-shown')
  }

  const hideLoading = () => {
    loading.classList.remove('loading-shown')
  }

  const showSearchUI = () => {
    searchForm.style.display = 'block'
    searchForm.scrollIntoView()
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
    totalResults = 0
    params.paged = 1
    params.catName = ''
    params.cat = false
    resultsList.innerHTML = initialCats
    disableSelects(false)
    Object.values(initialCatsSelectors).forEach(cat => {
      cat.addEventListener('click', searchCategory)
      cat.classList.remove('fade-out')
      cat.classList.add('fade-in')
    })
    hideSearchIU()
    if (localStorage) {
      localStorage.clear()
    }
  }

  const disableSelects = bool => {
    Object.values(searchFormSelects).forEach(el => {
      const select = el.children[1]
      if (bool) {
        const excludeFields = cats_array[params.cat] || false
        if (excludeFields && excludeFields.exclude.includes(select.id)) {
          select.disabled = bool
          el.style.display = 'none'
        }
      } else {
        select.children[0].selected = true
        select.disabled = bool
        el.style.display = 'block'
      }
    })
  }

  const saveSelections = form_data => {
    if (localStorage) {
      let object = {}
      form_data.forEach((value, key) => object[key] = value)
      const json = JSON.stringify(object)
      localStorage.setItem(localStorageKey, json)
    }
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
    totalResults = total
    if (debug) {
      console.info('Results found', data.length)
      console.info('Debug', debug)
    } 
    else if (data.length > 0) {
      Object.values(initialCatsSelectors).forEach(cat => {
        cat.removeEventListener('click', searchCategory)
        cat.classList.add('fade-out')
      })
      disableSelects(true)
      setTimeout(() => {
        resultsStats.innerHTML = `<h2>${params.catName} <small>${totalResults} matches found.</small></h2>`
        pageCount.innerText = `${params.paged}/${Math.floor(totalResults / params.postsPerPage)}`
        resultsList.innerHTML = ''
        data.forEach(obj => renderThumbnail(obj))
        showSearchUI()
      }, animationDuraton)
    } else {
      resultsStats.innerHTML = `<h2>${params.catName} <small>No matches found.</small></h2>`
      resultsList.innerHTML = ''
    }
  }

  const fetchLivestock = async form_data => {
    try {
      const response = await fetch(ajax_url, {
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

  const searchFormSubmit = e => {
    e.preventDefault()
    showLoading()
    let form_data = new FormData(searchForm)
    params.includeMeta = true
    for (key in params) {
      form_data.append(key, params[key])
    }
    saveSelections(form_data)
    fetchLivestock(form_data)
  }

  const searchCategory = e => {
    e.preventDefault()
    showLoading()
    const target = e.target
    const a = target.tagName === 'A' ? target : target.parentNode
    if (a.dataset.catid) {
      params.cat = a.dataset.catid
    }
    if (a.dataset.catname) {
      params.catName = a.dataset.catname
    }
    params.includeMeta = false
    let form_data = new FormData()
    for (key in params) {
      form_data.append(key, params[key])
    }
    fetchLivestock(form_data)
  }

  const goToPage = e => {
    params.paged += Number(e.target.dataset.dir)
    if (params.paged <= 0 || params.paged >= totalResults) return
    searchFormSubmit(e)
  }

  // init
  Object.values(initialCatsSelectors).forEach(cat => cat.addEventListener('click', searchCategory))
  Object.values(paginationButtons).forEach(el => el.addEventListener('click', goToPage))
  Object.values(searchFormSelects).forEach(el => searchCriteria.push(el.children[1].name))
  searchForm.addEventListener('submit', searchFormSubmit)
  resetButton.addEventListener('click', reset)

  if (shouldReset || catId) {
    reset()
    if (catId) {
      Object.values(initialCatsSelectors).forEach(el => {
        if (el.dataset.catid == catId) 
          el.click()
      })
    }
  }

  if (localStorage && localStorage.getItem(localStorageKey) !== null) {
    params = JSON.parse(localStorage.getItem(localStorageKey))
    Object.values(searchFormSelects).forEach(el => {
      const select = el.children[1]
      select.value = params[select.name]
    })
    searchFormSubmitButton.click()
  }

})()

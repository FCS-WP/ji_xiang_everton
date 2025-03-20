"use strict";
  $ = jQuery;

  $(document).ready(function() {
    const postCode = $('#input_postcode');

    if (!postCode.length) {
      return; 
    }

    let isLoading = false;

    // Create the results container element
    const createResultsContainer = () => {
        const resultContainer = document.getElementById('billing_postcode_field');
        const resultWrapper = document.createElement('div');
        resultWrapper.id = 'results-wrapper';
        resultContainer.appendChild(resultWrapper);
      };
      createResultsContainer();

    // Clear previous results
    const clearPreviousResults = (container) => {
        container.innerHTML = ''; // Clear previous results
      };

    // Display results in the results container
    const displayResults = (results) => {
        const resultWrapper = document.getElementById('results-wrapper');
        clearPreviousResults(resultWrapper);
  
        if (!results || results.length === 0) {
          displayNoResults(resultWrapper);
          return;
        }
  
        results.forEach(result => {
          const resultItem = createResultItem(result);
          resultWrapper.appendChild(resultItem);
        });
      };

    // Debounce function
    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
          if (timeoutId) {
            clearTimeout(timeoutId);
          }
          timeoutId = setTimeout(() => {
            func.apply(null, args);
          }, delay);
        };
      };

    // Debounced search function
    const debouncedSearch = debounce(async function(query) {
        const data = await handleOnChange(query);
        if (!data || data.length === 0) return;
  
        displayResults(data.results);
      }, 1000);

    // Create the results container

    // Event listener for input changes
    postCode.on('input', function(e) {
      e.preventDefault();
      const postCodeValue = postCode.val();
      debouncedSearch(postCodeValue);
    });

    //Append data for Fields

    const appendData = async (addressData, streetData,latitudeData, longitudeData) => {
      const address = $('#input_address_1');
      const latitude = $('#input_latitude_1');
      const longitude = $('#input_longitude_1');
      address.val(addressData);
      latitude.val(latitudeData);
      longitude.val(longitudeData);

    }
    //Append data for Fields

    const removeData = async () => {
      const address = $('#input_address_1');
      address.val('');

    }
    // Async function to handle input change
    const handleOnChange = async (params) => {
      if (isLoading) return;

      isLoading = true;
      const data = await fetchData(params);
      isLoading = false;
      return data;
    };

    // Async function to fetch data from the API
    const fetchData = async (params) => {
      const query = {
        searchVal: params,
        returnGeom: "Y",
        getAddrDetails: 'y'
      };
      const queryString = new URLSearchParams(query).toString();
      const url = `https://www.onemap.gov.sg/api/common/elastic/search?${queryString}`;

      try {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        return data;
      } catch (error) {
        console.error('Error fetching data:', error);
        return null; // Return null or handle the error appropriately
      }
    };


    // Display no results message
    const displayNoResults = (wrapper) => {
      wrapper.innerHTML = '<p>No results found.</p>';
    };

    // Create a result item
    const createResultItem = (result) => {
      const resultItem = document.createElement('div');
      resultItem.className = 'result-item';

      const removeIcon = createRemoveIcon(resultItem);
      resultItem.innerHTML = `
        <h3>${result.BUILDING || 'Address'}</h3>
        <p>${result.ADDRESS || 'No address available.'}</p>
      `;
      resultItem.appendChild(removeIcon);

      resultItem.addEventListener('click', () => handleItemClick(resultItem, result));
      return resultItem;
    };

    // Create a remove icon for the result item
    const createRemoveIcon = (resultItem) => {
      const removeIcon = document.createElement('span');
      removeIcon.className = 'remove-icon';
      removeIcon.innerHTML = '&times;';
      removeIcon.style.cursor = 'pointer';

      removeIcon.addEventListener('click', (e) => {
        e.stopPropagation();
        resultItem.remove();
        // remove data Address
        removeData();
      });

      return removeIcon;
    };

    // Handle item click to select it
    const handleItemClick = (resultItem, result) => {
      const resultWrapper = document.getElementById('results-wrapper');
      resultWrapper.innerHTML = ''; // Clear results

      resultWrapper.appendChild(resultItem); // Keep the selected item
      const allItems = document.querySelectorAll('.result-item');

      allItems.forEach(item => item.classList.remove('selected'));
      resultItem.classList.add('selected');

      appendData(result.ADDRESS, result.BUILDING, result.LATITUDE, result.LONGITUDE);   
    };

    

    
  });
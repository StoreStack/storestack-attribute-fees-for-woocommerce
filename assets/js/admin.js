'use strict';

document.addEventListener('DOMContentLoaded', () => {
   const attributeFeesData = document.getElementById('attribute_fees_data');

   if (!attributeFeesData) {
      return;
   }

   const elements = attributeFeesData.querySelectorAll('div[data-taxonomy]');
   const attributes = [];
   for (const div of elements) {
      attributes.push(div.dataset.taxonomy);
   }

   for (const attribute of attributes) {
      const changeAllSelect = document.getElementById(`ssaffw_${attribute}_change_all_select`);
      const changeAllButton = document.getElementById(`ssaffw_${attribute}_change_all_button`);

      changeAllButton.addEventListener('click', () => {
         const selected = changeAllSelect.value;
         const selectElements = document.querySelectorAll(`table.ssaffw-fees-table.${attribute} select`);
         for (const selectElement of selectElements) {
            selectElement.value = selected;
         }
      });
   }
});

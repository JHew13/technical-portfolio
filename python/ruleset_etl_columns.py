# Ruleset Grid Search Automation
# Automates a dynamic web grid, searches selected values, and exports matching data.
#
# Portfolio note:
# This is a sanitized public sample based on code I originally wrote for an internal
# business tool. Proprietary names, credentials, endpoints, identifiers, and business-
# specific values have been replaced with generic equivalents.
#
# Key concepts demonstrated:
# - Selenium browser automation
# - dynamic/virtualized grid handling
# - stale-element recovery
# - Excel output and data validation

def scrape_range(start_index, end_index):

    from selenium import webdriver
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support import expected_conditions as EC
    from selenium.common.exceptions import StaleElementReferenceException
    from selenium.webdriver.common.action_chains import ActionChains as Actions
    from selenium.common.exceptions import WebDriverException
    import pandas as pd
    import openpyxl
    import os
    import time

    # Values I want to look for in each ruleset grid
    search_terms = ["SEARCH_VALUE_1","SEARCH_VALUE_2"]

    # Internal application URL removed from this sample
    url = "https://example.invalid/app/ruleset-editor"
    driver = webdriver.Chrome()
    driver.get(url)
    # Set up the browser wait so the page has time to load before I interact with it
    wait = WebDriverWait(driver, 20)

    # Open the section that contains the ruleset browser
    wait.until(EC.element_to_be_clickable((By.XPATH, "//nav/ul/li[1]/a"))).click()

    wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, "ng-select span.ng-arrow-wrapper"))).click()
    time.sleep(1)  # Give dropdown panel time to open

    # Grab the available rulesets from the dropdown
    dropdown_options = driver.find_elements(By.CLASS_NAME, "ng-option")
    dropdown = wait.until(EC.element_to_be_clickable((By.XPATH,"/html/body/app-root/app-ruleset-browser/div[1]/div[1]/div/ng-select/div/span")))
    dropdown.click()
    dropdown_trigger = wait.until(EC.element_to_be_clickable((By.XPATH,"/html/body/app-root/app-ruleset-browser/div[1]/div[1]/div/ng-select/div/div/div[2]")))
    Actions(driver).move_to_element(dropdown_trigger).perform()
    #time.sleep(0.5)
    dropdown_trigger.click()
    #time.sleep(0.5)

    total_options = len(dropdown_options)
    print(f"Total dropdown options found: {total_options}")
    # Keep track of rulesets where I find one of the values I am looking for
    results = {}
    # Store the actual matches so I can export them to Excel at the end
    match_data = []
    for index in range(start_index, min(end_index, total_options)):
    # Reopen the dropdown each time because the page can replace the old elements
        try:
            wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, "ng-select span.ng-arrow-wrapper"))).click()
            time.sleep(0.5)
            dropdown_trigger.click()
            options = wait.until(EC.presence_of_all_elements_located((By.CLASS_NAME, "ng-option")))

            if index >= len(options):
                print("Index out of bounds — skipping")
                continue

            option_text = options[index].text.strip()
            print(f"\nSelecting option {index + 1}: {option_text}")
        except WebDriverException as e:
            print("WebDriverException... trying to refresh")
            driver.refresh()
            time.sleep(5)
            continue

        try:
            Actions(driver).move_to_element(options[index]).click().perform()
        except StaleElementReferenceException:
            print("Stale element during dropdown click — retrying")
            continue

        # Wait for the ruleset grid to load before trying to read it
        try:
            wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".ag-theme-balham .ag-row")))
        except:
            print("No data grid rows loaded")
            continue

        # Pull the column names so I can report which column contains the match
        header_cells = driver.find_elements(By.CSS_SELECTOR, ".ag-header-cell")
        column_headers = [cell.text.strip()for cell in header_cells]
        

        # Track rows I already read because the grid loads rows as I scroll
        seen_rows = set()
        scroll_container = driver.find_element(By.CSS_SELECTOR, "ag-grid-angular .ag-body-viewport")
        scroll_position = 0
        row_counter = 0
        stalled_scrolls = 0
        max_scrolls = 100

        # Scroll through the grid in sections so I can capture rows that are loaded dynamically
        while stalled_scrolls < 5 and scroll_position < 5000:
            driver.execute_script("arguments[0].scrollTop = arguments[1];", scroll_container, scroll_position)
            time.sleep(0.3)
            scroll_position += 250

            try:
                visible_rows = driver.find_elements(By.CSS_SELECTOR, ".ag-theme-balham .ag-row")
            except StaleElementReferenceException:
                print("Stale grid — refetching rows")
                continue

            new_rows_found = False
            for row in visible_rows:
                try:
                    cells = row.find_elements(By.CSS_SELECTOR, "span.ag-cell-value")
                    if not cells:
                        continue

                    cell_texts = [cell.text.strip() for cell in cells]
                    row_text = " | ".join(cell_texts)

                    # Skip anything I already picked up during an earlier scroll
                    if row_text in seen_rows:
                        continue

                    seen_rows.add(row_text)
                    row_counter += 1
                    print(f"Ruleset: {option_text}, Row {row_counter}: {row_text}")

                    # Check each cell and save the ruleset, value, and matching column
                    for idx, cell in enumerate(cell_texts):
                        if cell in search_terms:
                            matched_column = column_headers[idx] if idx < len(column_headers) else f"Column {idx+1}" 
                            print(f"match found in column, '{matched_column}':{cell}")
                            match_data.append({
                                "RuleSet Name": option_text,
                                "matched Value": cell,
                                "matched Results Column": matched_column
                            })
                        if any(term == cell for cell in cell_texts for term in search_terms):
                            print(f" MATCH found in Row {row_counter}: {row_text} in {matched_column}")

                            #results.setdefault(option_text, []).append(row_text)

                            new_rows_found = True
                            results[option_text] = True
                except StaleElementReferenceException:
                    continue

        if not new_rows_found:
            stalled_scrolls += 1
        else:
            stalled_scrolls = 0 

    # Put all of the matches into a spreadsheet for review
    #matched_options = list(results.keys())
    df = pd.DataFrame(match_data)
    filename = f"matched_rulesets_{start_index}_{end_index}.xlsx"
    df.to_excel(filename, index=False)
    print(f"Matched rulesets from {start_index} to {end_index}) saved to {filename}")
    driver.quit()
  

def main():
    import multiprocessing as mp

    # Keep this low while testing, then increase it when I am ready for the full run
    total = 10 #Change to full amount when test complete
    # Break the work into batches so multiple processes can work through the list
    batch_size = 10 #Adjust batch size as needed
    processes = []
    # Build the start/end ranges that will be passed to each process
    ranges = [(i,min(i + batch_size, total)) for i in range(0, total, batch_size)]
    
    processes = []

    for start, end in ranges:
        
        # Start a separate process for each range
        p = mp.Process(target=scrape_range, args=(start, end))
        p.start()
        processes.append(p)

    # Wait until every process is finished before ending the script
    for p in processes:
        p.join()
    
  



    print("saved results to excel")



if __name__ == "__main__":
    main()
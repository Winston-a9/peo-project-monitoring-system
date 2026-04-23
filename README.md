# PEO Document Monitoring System

A comprehensive project monitoring and documentation system designed for tracking construction projects, managing project timelines, and calculating project liabilities with precision.

---

## 📋 Table of Contents

- [System Overview](#system-overview)
- [Getting Started](#getting-started)
- [Key Features](#key-features)
- [Using the System](#using-the-system)
- [Common Tasks](#common-tasks)
- [Need Help](#need-help)

---

## 🎯 System Overview

The PEO Document Monitoring System helps you:
- **Track Projects** - Monitor project progress from start to completion
- **Manage Timelines** - Handle time extensions, suspensions, and delays
- **Calculate Penalties** - Automatically compute liquidated damages (LD) for overdue projects
- **Record Documents** - Maintain comprehensive records of all project documents and actions
- **Monitor Costs** - Track costs associated with variations and extensions

---

## 🚀 Getting Started

### 1. Login to the System
- Open the application in your web browser
- Enter your login credentials (username and password)
- Click **Sign In**

### 2. Navigate to Projects
- Once logged in, go to the **Projects** section from the main menu
- You'll see a list of all projects you have access to

### 3. Select a Project
- Click on any project to view its details
- You can edit the project by clicking the **Edit** button

---

## ✨ Key Features

### Project Dashboard
View all essential project information at a glance:
- **Project Title & Status** - Current state of the project
- **Contract Details** - Amount, dates, and milestones
- **Progress Metrics** - As Planned vs. Work Done percentages
- **Project Slippage** - Days behind schedule

### Liquidated Damages (LD) Assessment
Track penalties for project delays:
- **Accomplished %** - Percentage of work completed
- **Days Overdue** - Track from when the project fell behind schedule
- **Daily Rate** - Automatic calculation of LD per day
- **Total Liability** - Running total of accumulated penalties
- **Termination** - End penalties when delays are resolved

### Document Management
Record project documents and actions:
- **Time Extensions** - Add extensions with associated costs
- **Variation Orders** - Document scope changes with new costs
- **Suspension Orders** - Record project suspensions and their durations

### Progress Tracking
Monitor project advancement:
- **As Planned %** - Original planned completion percentage
- **Work Done %** - Actual work completed
- **Slippage** - Automatic calculation of days behind schedule

### Financial Tracking
Keep track of project finances:
- **Contract Amount** - Original contract value
- **Amount Billed** - Total billed to date
- **Remaining Balance** - Available budget minus costs
- **Cost Tracking** - Extension and variation order costs

---

## 📖 Using the System

### Viewing Project Details
1. Go to **Projects** in the main menu
2. Click on the project you want to view
3. Scroll through sections to see all information

### Editing Project Information
1. Click **Edit** button on the project detail page
2. Update the information in relevant sections
3. Click **Save** to save your changes

### Adding Time Extensions
1. Go to Edit mode for a project
2. Click the **Time Extensions** button
3. Fill in the extension details:
   - Number of days to extend
   - Cost involved (if any)
   - Reason or coverage notes
4. Click **Save**

### Adding Variation Orders
1. Go to Edit mode for a project
2. Click the **Variation Orders** button
3. Enter the variation details:
   - New cost for the variation
   - Reason or description
4. Click **Save**

### Recording Suspension Orders
1. Go to Edit mode for a project
2. Click the **Suspension Orders** button
3. Specify the number of suspension days
4. Add reason or notes
5. Click **Save**

### Calculating Liquidated Damages
1. Go to Edit mode for a project
2. Scroll to the **Liquidated Damages** section
3. Enter the **Accomplished %** (percentage of work done)
4. Enter the **Days Overdue (From)** date (when the project fell behind)
5. System automatically calculates:
   - Unworked %
   - LD per Day
   - Total LD (cumulative daily penalties)
6. To stop penalties, click **Terminate Penalty**

### Tracking Billing
1. From project view, see the **Billing Information**:
   - Total Amount Billed
   - Remaining Budget
   - Costs deducted for extensions and variations
2. Update billing amounts in Edit mode

### Adding Remarks
1. Go to Edit mode
2. Scroll to **Remarks & Recommendations** section
3. Add any observations, notes, or recommendations
4. Click **Save**

---

## 💡 Common Tasks

### Task: Update Project Progress
1. Navigate to project
2. Click **Edit**
3. Update the **Work Done %** field
4. System automatically recalculates slippage
5. Click **Save**

### Task: Assess Penalties for Late Project
1. Navigate to project in Edit mode
2. Go to **Liquidated Damages** section
3. Enter the work accomplished percentage
4. Enter the date when the project became overdue
5. Review the calculated daily rate and total liability
6. Click **Save**

### Task: End a Penalty Period
1. In **Liquidated Damages** section
2. Click **Terminate Penalty** button
3. Penalty calculations stop as of today's date
4. Click **Save**

### Task: Record Project Completion
1. Update **Work Done %** to 100%
2. Update **As Planned %** if needed
3. Add any final remarks
4. Click **Save**

### Task: Add Multiple Extensions
1. In **Time Extensions** section
2. Click **Add New Extension** as needed
3. Each extension can have different durations and costs
4. Click **Save**

---

## 📊 Understanding Key Metrics

### Slippage
- **Definition**: Days behind the original schedule
- **Calculation**: Automatic based on As Planned % vs Work Done %
- **Use**: Quickly identify delayed projects

### Liquidated Damages (LD)
- **Definition**: Daily penalty for project delays
- **Calculation**: (Accomplished % / 100) × Contract Amount × 0.001 × Days Overdue
- **Purpose**: Financial accountability for delays

### Remaining Balance
- **Definition**: Budget available after deducting costs
- **Calculation**: Contract Amount - Amount Billed - Extension Costs - Variation Costs
- **Use**: Monitor available project funds

---

## 🆘 Need Help?

### Common Issues

**I can't find my project**
- Check if the project appears in the main Projects list
- Use search or filter if available
- Contact your administrator if you should have access but don't

**The calculation seems wrong**
- Ensure all required dates are entered correctly
- Check that percentages are between 0-100
- Note that dates are calculated from the "Overdue From" date

**I accidentally updated something**
- Changes are saved immediately
- Contact your administrator to review change history if needed

**Progress bar shows incorrect slippage**
- Slippage updates automatically when you change Work Done %
- Refresh the page if you don't see the update immediately

### Getting Support
- Contact your system administrator
- Report issues with detailed project information
- Include the project name and the specific issue encountered

---

## 📝 Notes

- All amounts are in Philippine Pesos (₱)
- Dates use the format YYYY-MM-DD
- Percentages should be entered as numbers between 0 and 100
- The system automatically calculates derived fields (do not edit these manually)
- All changes are saved immediately to the database

---

**Version**: 1.0  
**Last Updated**: April 2026

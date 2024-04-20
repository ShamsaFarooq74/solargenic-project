"use strict";

function getCurrentDate() {
  var now = new Date();
  var day = ("0" + now.getDate()).slice(-2);
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var todayDate = now.getFullYear() + "-" + month + "-" + day;
  var todayMonth = now.getFullYear() + "-" + month;
  var todayYear = now.getFullYear();
  var objDate = {
    todayDate: todayDate,
    todayMonth: todayMonth,
    todayYear: todayYear
  };
  return objDate;
}

function formatDate(date) {
  var todayDate = new Date(date).toISOString().slice(0, 10);
  return todayDate;
}

function formatPreviousMonth(date) {
  var datess = new Date(date);
  datess.setDate(0);
  datess.setDate(1);
  datess = new Date(datess);
  var month = datess.getMonth() + 1;
  todayMonth = datess.getFullYear() + '-' + month;
  return todayMonth;
}

function formatForwardMonth(date) {
  var datess = new Date(date);
  datess.setMonth(datess.getMonth() + 1);
  datess = new Date(datess);
  var month = datess.getMonth() + 1;
  todayMonth = datess.getFullYear() + '-' + month;
  return todayMonth;
}

function formatPreviousYear(date) {
  var datess = new Date(date);
  datess.setFullYear(datess.getFullYear() - 1);
  var todayYear = datess.getFullYear();
  return todayYear;
}

function formatForwardYear(date) {
  var datess = new Date(date);
  datess.setFullYear(datess.getFullYear() + 1);
  var todayYear = datess.getFullYear();
  return todayYear;
}
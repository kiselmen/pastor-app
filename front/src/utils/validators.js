export const EMAIL_REGEXP = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/iu

export function isEmailValid (value) {
  return EMAIL_REGEXP.test(value)
}

export const PHONE_REGEXP = /\+7\s\(\d{3}\)\s\d{3}-\d{2}-\d{2}/

export function isPhoneValid (value) {
  return PHONE_REGEXP.test(value)
}

export const PRICE_REGEXP = /^[0-9]+((,|.)[0-9]{2})?$/

export function isPriceValid (value) {
  return PRICE_REGEXP.test(value)
}

const rules = {
  '#': /\d/g, // numbers
  '@': /[a-zA-Zа-яА-Я]/g // letters
}

const keys = ['#', '@']

export function masking (value, mask) {
  if (!mask || !value) {
    return value
  }
  const maskSymbols = mask.replace(/#/g, '').replace(/@/g, '').split('')
  const valueArr = value.split('')
  const maskArr = mask.split('')
  if (!valueArr || valueArr.length === 0) {
    return ''
  }
  const result = []
  let skipCount = 0
  let itt = 0
  for (let i = 0; i < valueArr.length; i++) {
    if (itt >= maskArr.length + valueArr.length) {
      break
    }
    itt += 1
    if (valueArr[i] === maskArr[i + skipCount]) { // если символ в строке совпадает с маской
      result.push(valueArr[i])
      continue
    }
    if (keys.includes(maskArr[i + skipCount])) { // @ts-ignore если символ в маске является ключом
      const val = valueArr[i].match(rules[maskArr[i + skipCount]])
      if (!val || val[0] === '') {
        skipCount -= 1
      } else {
        result.push(val[0])
      }
      continue
    }
    if (valueArr[i] !== maskArr[i + skipCount]) { // если символ в строке не совпадает с маской
      result.push(maskArr[i + skipCount])
      skipCount += 1
      i -= 1
    }
  }
  return result.filter(r => !!r).join('')
}

export function maskNumber (value, mask) {
  if (!mask || !value) {
    return value
  }
  const maskArr = mask.split('')
  const symbs = uniq(mask.replace(/#/g, '').split(''))
  const val = value.split('')
  const _rawValue = []
  for (let i = 0; i < val.length; i++) {
    if (!symbs.includes(val[i]) || val[i] !== maskArr[i]) {
      _rawValue.push(val[i])
    }
  }
  const rawValue = _rawValue.join('').match(/\d/g)
  // const rawValue = value.match(/\d/g)
  if (rawValue && rawValue.length !== 0) {
    const result = []
    let count = 0
    for (let i = 0; i < maskArr.length; i++) {
      if (count === rawValue.length) {
        break
      }
      if (maskArr[i] === '#') {
        result.push(rawValue[count])
        count++
      } else {
        result.push(maskArr[i])
      }
    }
    return result.join('')
  } else {
    return ''
  }
}

function uniq () {
  const seen = {}
  const out = []
  const len = arr.length
  let j = 0
  for (let i = 0; i < len; i++) {
    const item = arr[i] // @ts-ignore
    if (seen[item] !== 1) { // @ts-ignore
      seen[item] = 1
      out[j++] = item
    }
  }
  return out
}

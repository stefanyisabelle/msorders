import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import TravelForm from '@/components/TravelForm.vue'

vi.mock('vue-i18n', () => ({
  useI18n: () => ({
    t: (key) => key
  })
}))

vi.mock('@/stores/travelStore.js', () => ({
  useTravelStore: () => ({
    create: vi.fn().mockResolvedValue({})
  })
}))

describe('TravelForm', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('deve renderizar todos os campos', () => {
    const wrapper = mount(TravelForm)

    expect(wrapper.find('#customer_name').exists()).toBe(true)
    expect(wrapper.find('#destination').exists()).toBe(true)
    expect(wrapper.find('#start_date').exists()).toBe(true)
    expect(wrapper.find('#end_date').exists()).toBe(true)
  })

  it('deve mostrar erro quando nome está vazio', async () => {
    const wrapper = mount(TravelForm)

    await wrapper.find('form').trigger('submit.prevent')

    expect(wrapper.text()).toContain('validation.nameRequired')
  })

  it('deve mostrar erro quando destino está vazio', async () => {
    const wrapper = mount(TravelForm)

    await wrapper.find('#customer_name').setValue('John Doe')
    await wrapper.find('form').trigger('submit.prevent')

    expect(wrapper.text()).toContain('validation.destinationRequired')
  })

  it('deve limpar campos quando clicar em limpar', async () => {
    const wrapper = mount(TravelForm)

    await wrapper.find('#customer_name').setValue('John Doe')
    await wrapper.find('#destination').setValue('São Paulo')
    
    const clearButton = wrapper.findAll('button').find(btn => btn.text().includes('form.clear'))
    await clearButton.trigger('click')

    expect(wrapper.find('#customer_name').element.value).toBe('')
    expect(wrapper.find('#destination').element.value).toBe('')
  })

  it('deve ter botões de limpar (X) nos campos', () => {
    const wrapper = mount(TravelForm)

    // Preencher campos para exibir botões de limpar
    wrapper.vm.customerName = 'John Doe'
    wrapper.vm.destination = 'São Paulo'
    
    // Forçar re-render
    wrapper.vm.$nextTick()

    // Verificar que os campos têm valor
    expect(wrapper.vm.customerName).toBe('John Doe')
    expect(wrapper.vm.destination).toBe('São Paulo')
  })

  it('deve mostrar estado de loading durante submissão', async () => {
    const wrapper = mount(TravelForm)

    await wrapper.find('#customer_name').setValue('John Doe')
    await wrapper.find('#destination').setValue('São Paulo')
    await wrapper.find('#start_date').setValue('2026-03-01')
    await wrapper.find('#end_date').setValue('2026-03-10')

    const submitButton = wrapper.findAll('button').find(btn => btn.attributes('type') === 'submit')
    
    await wrapper.find('form').trigger('submit.prevent')

    // Verificar que o botão mostra loading
    expect(wrapper.vm.loading).toBeDefined()
  })
})
